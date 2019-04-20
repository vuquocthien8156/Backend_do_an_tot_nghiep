'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#chat-user',
    components: { Pagination },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results_info_user: {info_user: [[{name: "", avatar_path: "", id: ""}]]},
            id_user: '',
            messages: [{from: "", content: ""}],
            image_user: '',
            content_chat: '',
            admin_id: '',
            style_Image_Chat: { 'margin-left' : '50%' },
            action_change: [],
            key_conversation: '',
            keyEndAT: '',
            //
            data_user: [],
            data_user_tmp: [],
            check_path_avatar: '',

            // Last message
            type_message: '',
            arr_last_message: [],

            // Number message not seen
            arr_not_seen_message: [],
            array_user_number_mesage_not_seen: [],

            //Get limit number user
            number_user : 10,
            limitMessage: 15,
        };
    },
    created() { },
    mounted() {
        var admin_id = $('#image_upload').data('id_admin');
        this.admin_id = admin_id;
        common.loading.show('body');
        
        this.getListUser().then(() => {
            this.data_user.reverse();
            var id_user = this.data_user[0].id_user;
            var image_user = this.data_user[0].avatar;
            this.getDataConversationFirebase(id_user, image_user);
            this.data_user.forEach(element => {
                this.getMessageOld(element.id_user);
                this.countMessageNotSeen(element.id_user);
            });
        });
        setTimeout(() => {
            this.data_user.forEach(element => {
                var nb = 0;
                this.arr_not_seen_message.forEach(el => {
                    if(element.id_user == el.id_user) {
                        nb = nb + 1;
                    }
                });
                this.array_user_number_mesage_not_seen.push({
                    id_user: element.id_user,
                    number_mesage_not_seen: nb,
                });
            });
        }, 3000);

        $('.write_msg').on("keydown", (event) => {
            if(event.which === 13) {
                this.pushContentChat();
            }
        });
        this.scrollLoadMoreMessage();
    },
    methods: {
        getListUser() {
            return new Promise((resolve) => {
                common.checkFirebaseReady().then(() => {
                    firebase.database().ref('users').on('child_added', (data) => {
                        var check_path_avatar_user = 0;
                        var image_user = data.val().avatar;
                        if (image_user != undefined ) {
                            var str_search_path = data.val().avatar.search("http");
                            if(image_user != "" || image_user != null) {
                                if (str_search_path != -1) {
                                    check_path_avatar_user = 1; // path image fb or gg
                                } else {
                                    if(image_user != "") {
                                        check_path_avatar_user = 2; // path image user in DB
                                    } else {
                                        check_path_avatar_user = 0;
                                    }
                                }
                            } else {
                                check_path_avatar_user = 0;
                            }
                        }
                        this.data_user.push({
                            id_user: data.key,
                            name: data.val().name,
                            avatar: data.val().avatar,
                            check_path_avatar_user : check_path_avatar_user,
                        });
                        resolve();
                    });  
                });
            });
        },

        getDataConversationFirebase(id_user, images_path) {
            this.id_user = id_user;
            this.messages = [];
            if (images_path != undefined ) {
                var str_search = images_path.search("http");
                if(images_path != '' || images_path != null) {
                    if (str_search != -1) {
                        this.check_path_avatar = 0; // path image fb or gg
                    } else {
                        this.check_path_avatar = 1; // path image user in DB
                    }
                } else {
                    this.check_path_avatar = 2; // path image defaut
                }
            }
            this.image_user = images_path;
            var chk = false;
            common.checkFirebaseReady().then(() => {
                firebase.database().ref('conversations').on('child_added', (data) => {
                    let childData = data.val();
                    var obj = childData.members;

                    if((obj['admin'] == this.admin_id && obj['member'] == this.id_user) || ( obj['member'] == this.admin_id && obj['admin'] == this.id_user)) {
                        this.key_conversation = data.key;
                        chk = true;
                    }
                });
                if (this.key_conversation != null || this.key_conversation != undefined) {
                    this.getMessageConversation(id_user);
                }
            }).then(() => {
                setTimeout(() => {
                    if(chk == false) {
                        const roomKey = firebase.database().ref(`conversations/`)
                        .push({
                            members: {
                                admin: parseInt(this.admin_id),
                                member: parseInt(id_user)
                            },
                        }).key;
                        this.key_conversation = roomKey;
                        if (this.key_conversation != null || this.key_conversation != undefined) {
                            this.getMessageConversation(id_user);
                        }
                    }
                }, 2000);
            });
            setTimeout(() => {
                $('.msg_history').animate({
                    scrollTop: $('.msg_history').get(0).scrollHeight,
                }, 800);
                // Get the modal
                var modal = document.getElementById('myModal_chat');
                var modalImg = document.getElementById("img01");

                $('.image_chat').bind('click', function() {
                    modal.style.display = "block";
                    modalImg.src = this.src;
                });
                var span = document.getElementsByClassName("close_chat")[0];

                span.onclick = function() { 
                    modal.style.display = "none";
                };
            }, 800);
        },
        getMessageConversation(id_user) {
            var value_ = firebase.database().ref('conversations/' + this.key_conversation + '/messages/').limitToLast(this.limitMessage).on('child_added', (data) => {
                this.keyEndAT = data.key;
                let childData = data.val();
                var dt_ = new Date(parseInt(data.key));
                var date_time_chat = dt_.getHours() + ":" + (dt_.getMinutes() < 10 ? '0' : '') + dt_.getMinutes() + '  |  ' + dt_.getMonth() + 1 + '-' + dt_.getDate();
                var images = null;
                var content = null;
                var action = null;
                if (childData.images != 0) {
                    images = childData.images;
                }
                if (childData.content != undefined) {
                    content = childData.content;
                }
                if (childData.action != undefined) {
                    action = childData.action;
                }
                this.messages.push({
                    from: childData.from,
                    content: content,
                    images: images,
                    action: action,
                    date_time_chat: date_time_chat,
                    key: data.key,
                });
            });
            firebase.database().ref('conversations/' + this.key_conversation + '/messages/').off('value', value_);

            firebase.database().ref('conversations/' + this.key_conversation + '/messages/').on('child_changed', (data) => {
                let childData = data.val();
                if (childData.action == 1) {
                    $("#" + data.key).text('ĐÃ XÁC NHẬN');
                    $("#" + data.key).removeClass('text-primary');
                    $("#" + data.key).addClass('text-success');
                } else if(childData.action == 0) {
                    $("#" + data.key).text('ĐÃ TỪ CHỐI');
                    $("#" + data.key).removeClass('text-primary');
                    $("#" + data.key).addClass('text-danger');
                }

                if (childData.is_seen == 1) {
                    $("#number_ms_not_seen_" + id_user).addClass('d-none');
                }
            });
            setTimeout(() => {
                firebase.database().ref('conversations/' + this.key_conversation + '/messages/').once("value", function(snapshot) {
                    snapshot.forEach(function(child) {
                        if (child.val().from == id_user) {
                            child.ref.update({
                                is_seen: true
                            });
                        }
                    });
                });
            }, 1000);
            common.loading.hide('body');
        },
        countMessageNotSeen(id_user) {
            return new Promise((resolve) => {
                common.checkFirebaseReady().then(() => {
                    firebase.database().ref('conversations').on('child_added', (data) => {
                        let childData = data.val();
                        var childMessage = childData.messages;
                        for(var index in childMessage) {
                            if (childMessage[index].from == id_user && childMessage[index].is_seen == false) {
                                this.arr_not_seen_message.push({
                                    id_user: id_user,
                                    nb_not_seen: childMessage[index].is_seen,
                                });
                                resolve();
                            }
                        }
                    
                    });
                });
            });
        },
        getMessageOld(id_user) {
            common.checkFirebaseReady().then(() => {
                firebase.database().ref('conversations').on('child_added', (data) => {
                    let childData = data.val();
                    var obj = childData.members;
                    if((obj['admin'] == this.admin_id && obj['member'] == id_user) || ( obj['member'] == this.admin_id && obj['admin'] == id_user)) {
                        var content = childData.last_message;
                        var dt_ = new Date(parseInt(childData.updated_at));
                        var date_time_chat = dt_.getHours() + ":" + (dt_.getMinutes() < 10 ? '0' : '') + dt_.getMinutes() + '  |  ' + dt_.getMonth()+ 1 + '-' + dt_.getDate();
                        if(date_time_chat != undefined && content != undefined) {
                            $('#chat_date_time_' + id_user).text(date_time_chat);
                            $('#old_message_' + id_user).text(content);
                        }
                    }
                });
            }).then(() => {
                common.loading.hide('body');
            });
        },

        pushContentChat() {
            var date_ = new Date().getTime();
            var admin_id = $('#image_upload').data('id_admin');
            if(this.content_chat == "" || admin_id == "") {
                return false;
            }
            common.checkFirebaseReady().then(() => {
                firebase.database().ref('conversations/'+ this.key_conversation +'/messages/'+ date_).set({
                    content: this.content_chat,
                    from: admin_id,
                    is_seen: false,
                });
                firebase.database().ref('conversations/'+ this.key_conversation).update({
                    updated_at: date_,
                });
                firebase.database().ref('conversations/'+ this.key_conversation).update({
                    last_message: this.content_chat
                });
                setTimeout(() => {
                    this.content_chat = "";
                }, 500);
            });
            $('.msg_history').animate({
                scrollTop: $('.msg_history').get(0).scrollHeight
            }, 800);
        },

        filterSearchUser() {
            var data_arr = [];
            var queryText = $('#search-bar-user').val();
            firebase.database().ref('users').orderByChild("name").startAt(queryText).endAt(queryText + "\uf8ff").limitToFirst(10).once("value", function(data) { 
                let childData = data.val();
                for(var index in childData) {
                    var check_path_avatar_user = 0;
                    var image_user = childData[index].avatar;
                    if (image_user != undefined ) {
                        var str_search_path = childData[index].avatar.search("http");
                        if(image_user != "" || image_user != null) {
                            if (str_search_path != -1) {
                                check_path_avatar_user = 1; // path image fb or gg
                            } else {
                                if(image_user != "") {
                                    check_path_avatar_user = 2; // path image user in DB
                                } else {
                                    check_path_avatar_user = 0;
                                }
                            }
                        } else {
                            check_path_avatar_user = 0;
                        }
                    }
                }
            }); 
        },

        scrollLoadMoreMessage() {
            var lastScrollTop = 0;
            var that = this;
            $('.msg_history').scroll(function() {
                var st = $(this).scrollTop();
                if (st == 0) {
                    that.getMoreMessages();
                }
                lastScrollTop = st;
            });
        },

        getMoreMessages() {
            $('.loading_image_messages').removeClass('d-none');
            $('.loading_image_messages').addClass('d-flex');
            var arrMessageMore = [];
            this.limitMessage = this.limitMessage + 20;
            common.checkFirebaseReady().then(() => {
                var onValueChange  = firebase.database().ref('conversations/' + this.key_conversation + '/messages/').limitToLast(this.limitMessage).on('child_added', (data) => {
                    let childData = data.val();
                    var dt_ = new Date(parseInt(data.key));
                    var date_time_chat = dt_.getHours() + ":" + (dt_.getMinutes() < 10 ? '0' : '') + dt_.getMinutes() + '  |  ' + dt_.getMonth() + 1 + '-' + dt_.getDate();
                    var images = null;
                    var content = null;
                    var action = null;
                    if (childData.images != 0) {
                        images = childData.images;
                    }
                    if (childData.content != undefined) {
                        content = childData.content;
                    }
                    if (childData.action != undefined) {
                        action = childData.action;
                    }
                    arrMessageMore.push({
                        from: childData.from,
                        content: content,
                        images: images,
                        action: action,
                        date_time_chat: date_time_chat,
                        key: data.key,
                    });
                });
                setTimeout(() => {
                    firebase.database().ref('conversations/' + this.key_conversation + '/messages/').off('child_added', onValueChange);
                }, 1000);
                setTimeout(() => {
                    this.messages = [];
                    this.messages = arrMessageMore;
                    $('.loading_image_messages').removeClass('d-flex');
                    $('.loading_image_messages').addClass('d-none');
                }, 3000);
            });
        }
    }
});