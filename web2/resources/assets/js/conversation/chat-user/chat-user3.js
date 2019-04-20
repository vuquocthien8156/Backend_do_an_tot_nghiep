'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#chat-user3',
    components: { Pagination },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            style_Image_Chat: { 'margin-left' : '50%' },
            admin_id: '',
            number_of_unseen_messages_user_id_now : '',
            chats: [],
            members: [],
            messages: [],
            conversation_id_now: null,
            list_conversation_id: [],
            info_user_has_convesation: [],
            info_user_not_has_convesation: [],
            arr_image_str: [],
            chats_new_user: [],
            image_upload: null,
            id_user_firebase_now: '',
            path_resouce: '',
            user_id_no_hash_conversation: false,
            user_name_no_hash_conversation: '',
            id_conversation_new_for_user: null,
            limitMessage: 20,
        };
    },
    mounted() {
        $('.write_msg').on("keydown", (event) => {
            if(event.which === 13) {
                this.sendMessage();
            }
        });
        // $('#user_modal').select2();
        $('#user_modal').on('select2:select', () => {
            var id_user = $('#user_modal').val();
            var info_user_ = $('#select2-user_modal-container').text().split('/', 2);
            var name = info_user_[0].toString();
            this.checkUserIdHasConveration(id_user, name).then(() => {
                $('#user_modal').text('');
            });
        });
        $('#user_modal').select2({
            tags: [],
            minimumInputLength: 1,
            ajax: {
                url: '/conversation/search-info-user',
                dataType: 'json',
                type: "GET",
                data: function (term) {
                    return {
                        term: term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.searchInfoUser, function (item) {
                            return {
                                text: item.name + '/' + item.phone,
                                id: item.id,
                                data: item.name,
                            };
                        })
                    };
                },
            },
        });
        
    },
    created() { 
        var admin_id = $('#image_upload').data('id_admin');
        var path_resouce = $('#path_resouce').val();
        this.admin_id = admin_id;
        this.path_resouce = path_resouce;
        this.getChats().then(() => {
            // Get message by conversation_id
            this.conversation_id_now = this.chats[0].conversation_id;
            //this.id_user_firebase = this.chats[0].id_user_firebase;
            this.getMessages(this.conversation_id_now, true).then(() => {
                // Get the modal
                
                // $('.msg_history').animate({
                //     scrollTop: $('.msg_history').get(0).scrollHeight
                // }, 800);

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

                $('.msg_history').animate({  scrollTop: $('.msg_history').get(0).scrollHeight });
                this.scrollTopLoadMoreMessage();
            });
            this.getMembers(this.conversation_id_now);
            this.numberMessageNotSeen();
            ///Get Infor User Has Conversation
            this.getInfoUser(this.list_conversation_id).then(() => {    
                this.info_user_has_convesation.forEach((elm1, index1) => {
                    let conversation_id_origin = `c${elm1.conversation_id}`;
                    this.chats.forEach((elm2, index2) => {
                        if(elm2.conversation_id == conversation_id_origin) {
                            elm2.name_conversation = elm1.name;
                            if (elm1.avatar_path) {
                                elm2.avatar_path = `${this.path_resource}/${elm1.avatar_path}`;
                            } else {
                                elm2.avatar_path = '/images/user.png';
                            }
                        }
                    })
                });
            });
        }); 
    },
    updated() {
        $(".chat_list").on("click", function() {
            $(".chat_list").removeClass("active_chat");
            $(this).addClass("active_chat");
        });

        this.getInfoUser(this.list_conversation_id).then(() => {
            this.info_user_has_convesation.forEach((elm1, index1) => {
                let conversation_id_origin = `c${elm1.conversation_id}`;
                this.chats.forEach((elm2, index2) => {
                    if(elm2.conversation_id == conversation_id_origin) {
                        elm2.name_conversation = elm1.name;
                        if (elm1.avatar_path) {
                            elm2.avatar_path = `${this.path_resource}/${elm1.avatar_path}`;
                        } else {
                            elm2.avatar_path = '/images/user.png';
                        }
                    }
                })
            });
        });
    },
    methods: {
        getChats() {
            return new Promise((resolve) => {
                common.checkFirebaseReady().then(() => {
                    firebase.database().ref(`chats`)
                        .orderByChild('last_updated_at')
                        .limitToLast(100)
                        .on('value', (data) => {
                        let arr = [];
                        data.forEach((child) => {
                            if (child.val()[`u${this.admin_id}`] != undefined) {
                                arr.splice(0, 0, {
                                    conversation_id: child.key,
                                    last_messages: child.val().last_messages.content,
                                    time_last_message: this.formatTimestamp(child.val().last_messages.timestamp),
                                    last_updated_at: this.formatTimestamp(child.val().last_updated_at),
                                    avatar_path: null,
                                    name_conversation: null,
                                    number_of_unseen_messages_admin: 0,
                                    id_user_firebase : 45,
                                });
                                this.list_conversation_id.push({
                                    conversation_id: child.key,
                                });
                                resolve();
                            }
                        });
                        this.chats = arr;
                    });
                });
            });
        },

        getMembers(conversation_id) {
            common.checkFirebaseReady().then(() => {
                firebase.database().ref(`members/${conversation_id}`)
                    .on('value', (data) => {
                    Object.keys(data.val()).forEach((element, index) => {
                        if (element != `u${this.admin_id}`) {
                            this.number_of_unseen_messages_user_id_now = data.val()[element].number_of_unseen_messages;
                            this.id_user_firebase_now = element;
                        }
                    });
                });
            });
        },

        numberMessageNotSeen() {
            common.checkFirebaseReady().then(() => {
                firebase.database().ref(`members`)
                    .on('value', (data) => {
                    let childData = data.val();
                    Object.keys(childData).map((objectKey, index) => {
                        var value = childData[objectKey];
                        Object.keys(value).forEach((element, index2) => {
                            if (element == `u${this.admin_id}`) {
                                if(value[element].number_of_unseen_messages == 0) {
                                    $("#number_message_not_seen_" + objectKey).text("");
                                } else {
                                    $("#number_message_not_seen_" + objectKey).text(value[element].number_of_unseen_messages);
                                }
                            }
                        });
                    })
                });
            });
        },

        getMessages(conversation_id, tmp) {
            this.limitMessage = 20;
            firebase.database().ref(`messages/${conversation_id}`)
                .off('value');
            if (tmp == false) {
                this.messages = [];
                return true;
            }
            this.chats_new_user = [];
            this.conversation_id_now = conversation_id;
            this.user_id_no_hash_conversation = false;
            this.user_name_no_hash_conversation = null;
            return new Promise((resolve) => {
                common.checkFirebaseReady().then(() => {
                    firebase.database().ref(`messages/${conversation_id}`).limitToLast(this.limitMessage).on('value', (data) => {
                        let childData = data.val();
                        var arr_ = [];
                        
                        Object.keys(childData).map((objectKey, index) => {
                            var value = childData[objectKey];
                            var content = '';
                            var receiver_resource_text = '';
                            var receiver_resource_class = '';
                            
                            if (value.message_type == 2) {
                                content = value.content.split(","); 
                            } else {
                                content = value.content;
                            }

                            if (value.receiver_resource_action == 0) {
                                receiver_resource_text = 'CHƯA XÁC NHẬN'; 
                                receiver_resource_class = 'text-primary';

                            } else if (value.receiver_resource_action == 1) {
                                receiver_resource_text = 'ĐÃ ĐỒNG Ý'; 
                                receiver_resource_class = 'text-success';

                            } else {
                                receiver_resource_text = 'ĐÃ TỪ CHỐI'; 
                                receiver_resource_class = 'text-danger';
                            }
                            arr_.push({
                                conversation_id: conversation_id,
                                time_chat: this.formatTimestamp(value.timestamp),
                                message: content,
                                message_type: value.message_type,
                                from_user_id: value.from_user_id,
                                receiver_seen: value.receiver_seen,
                                receiver_resource_class: receiver_resource_class,
                                receiver_resource_action: receiver_resource_text,
                            });
                        });
                        this.messages = arr_;
                        resolve();
                    });
                    $('.msg_history').animate({  scrollTop: $('.msg_history').get(0).scrollHeight });
                    setTimeout(() => {
                        $('.msg_history').animate({
                            scrollTop: $('.msg_history').get(0).scrollHeight
                        }, 800);
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
                    }, 500);
                    setTimeout(() => {
                        firebase.database().ref(`members/${this.conversation_id_now}/u${this.admin_id}`).update({
                            number_of_unseen_messages: 0,
                        });
                    }, 3000);
                });
            });
        },

        getInfoUser(list_conversation_id) {
            return new Promise((resolve) => {
                if (list_conversation_id) {
                    var data = {
                        _token: this.csrf,
                        list_conversation_id : list_conversation_id,
                    };
                    $.post('/conversation/info-user', data)
                        .done(response => {
                            if (response.error === 0) {
                                this.info_user_has_convesation = response.info_user;
                                this.path_resource = response.path_resource;
                                resolve();
                            }
                        }).fail(error => {
                        }).always(() => {
                            common.loading.hide('body');
                        });
                }
                
            });
        },
        
        pushContent(content_chat, admin_id, date_, message_type, conversation_id_now, id_user_firebase) {
            return new Promise((resolve) => {
                firebase.database().ref(`messages/${conversation_id_now}`).push().set({
                    content: content_chat,
                    from_user_id: admin_id,
                    message_type: message_type,
                    receiver_seen: false,
                    timestamp: date_,
                    receiver_resource_action: 0,
                });
                let content_chat_last_message = (message_type == 2) ? '[Hình ảnh]' : content_chat;
                firebase.database().ref(`chats/${conversation_id_now}/last_messages`).update({
                    content: content_chat_last_message,
                    timestamp: date_,
                });
                firebase.database().ref(`chats/${this.conversation_id_now}`).update({
                    last_updated_at: date_,
                });
                firebase.database().ref(`members/${conversation_id_now}/u${this.admin_id}`).update({
                    number_of_unseen_messages: 0,
                });
                firebase.database().ref(`members/${conversation_id_now}/${id_user_firebase}`).update({
                    number_of_unseen_messages: this.number_of_unseen_messages_user_id_now + 1,
                });
                $('.inbox_chat').animate({ scrollTop: 0 }, 'slow');
                resolve();
            });
        },

        createNodeChatInFirebase(id_new_conversation, content_chat, message_type, admin_id, id_user, date_) {
            return new Promise((resolve) => {
                common.checkFirebaseReady().then(() => {
                    firebase.database().ref(`messages/c${id_new_conversation}`).push().set({
                        content: content_chat,
                        from_user_id: admin_id,
                        message_type: message_type,
                        receiver_seen: false,
                        timestamp: date_,
                        receiver_resource_action: 0,
                    });
                    let content_chat_last_message = (message_type == 2) ? '[Hình ảnh]' : content_chat;
                    firebase.database().ref(`chats/c${id_new_conversation}`).set({
                        [`u${admin_id}`]: true,
                        [`u${id_user}`]: true,
                        last_updated_at: date_,
                        deleted: {
                            [`u${admin_id}`]: false,
                            [`u${id_user}`]: false,
                        },
                        last_messages: {
                            content: content_chat_last_message,
                            timestamp: date_,
                        }
                    });
                    firebase.database().ref(`members/c${id_new_conversation}/u${admin_id}`).set({
                        number_of_unseen_messages: 0,
                        deleted_conversation: false,
                    });
                    firebase.database().ref(`members/c${id_new_conversation}/u${id_user}`).set({
                        number_of_unseen_messages: 1,
                        deleted_conversation: false,
                    });
                });
                resolve();
            });
        },

        createConversationInDB(id_user, name) {
            return new Promise((resolve) => {
                var data = {
                    user_id: id_user,
                    name: name.toString(),
                };
                $.post('/conversation/create-conversation', data)
                .done(response => {
                    if (response.error === 0) {
                       this.id_conversation_new_for_user = response.id_conversation;
                    }
                    resolve();
                });
            });
        },

        sendMessage() {
            let date_ = new Date().getTime();
            let admin_id = $('#image_upload').data('id_admin');
            let content_chat = $('#content_chat').val();
            let message_type = 1;
            if(content_chat == "" || admin_id == "") {
                return false;
            }
            if (this.user_id_no_hash_conversation != false) {
                this.createConversationInDB(this.user_id_no_hash_conversation, this.user_name_no_hash_conversation).then(() => {
                    this.createNodeChatInFirebase(this.id_conversation_new_for_user, content_chat, message_type, admin_id, this.user_id_no_hash_conversation, date_).then(() => {
                        this.chats_new_user = [];
                        $('#content_chat').val('');
                        $(".chat_list").removeClass("active_chat");
                        setTimeout(() => {
                            $('#c' + this.id_conversation_new_for_user).addClass("active_chat");
                        }, 800)
                        this.getMessages(`c${this.id_conversation_new_for_user}`, true);
                    });
                });
            } else {
                this.pushContent(content_chat, admin_id, date_, message_type, this.conversation_id_now, this.id_user_firebase_now).then(() => {
                    $('#content_chat').val('');
                    $('.msg_history').animate({
                        scrollTop: $('.msg_history').get(0).scrollHeight
                    }, 800);
                });
            }
            
        },

        handleUploadImage(evt) { // upload image to sever then get link path
            let result_promise =  new Promise((resolve) => {
                $('.loading_image').removeClass('d-none');
                $('.loading_image').addClass('d-block');
                $('.msg_history').animate({  scrollTop: $('.msg_history').get(0).scrollHeight });
                let arr_Promise_all = [];
                for (let i = 0; i < evt.target.files.length; i++) {
                    var formData = new FormData();
                    formData.append('file_image', evt.target.files[i]);
                    formData.append('conversation_id', this.conversation_id_now.toString().replace('c', ''));
                    let url = '/conversation/save-path-avatar';
                    let options = {
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                    };
                   let ajx = $.ajax(url, options).done(response => {
                        this.arr_image_str.push(response);
                    })
                    .always(() => {
                        common.loading.hide('body');
                    });
                    arr_Promise_all.push(ajx);
                }
                Promise.all(arr_Promise_all).then(() => {
                    resolve();
                })
            });
            result_promise.then(() => {
                let date_ = new Date().getTime();
                let message_type = 2; // type message image
                this.pushContent(this.arr_image_str.toString(), this.admin_id, date_, message_type, this.conversation_id_now, this.id_user_firebase_now).then(() => {
                    $('#content_chat').val('');
                    $('.msg_history').animate({
                        scrollTop: $('.msg_history').get(0).scrollHeight
                    }, 800);
                    $('.loading_image').removeClass('d-block');
                    $('.loading_image').addClass('d-none');
                });
            });
        },

        checkUserIdHasConveration(id_user, name_conversation) {
            return new Promise((resolve) => {
                var data = {
                    id_user: id_user
                };
                $.post('/conversation/check-user-has-conversation', data)
                .done(response => {
                    if (response.has_conversation === false) {
                        this.chats_new_user = [];
                        this.user_id_no_hash_conversation = id_user;
                        this.user_name_no_hash_conversation = name_conversation;
                        this.chats_new_user.unshift({
                            conversation_id: "",
                            time_chat:"",
                            message: "",
                            message_type: "",
                            from_user_id: "",
                            receiver_seen: "",
                            receiver_resource_class: "",
                            receiver_resource_action: "",
                            name_conversation: name_conversation,
                            avatar_path: '/images/user.png'
                        });
                        $(".chat_list").removeClass("active_chat");
                        this.messages = [];
                    } else {
                        let conversation_id_firebase = `c${response.info_conversation[0].conversation_id}`;
                        this.getMessages(conversation_id_firebase, true);
                        $(".chat_list").removeClass("active_chat");
                        $('#'+conversation_id_firebase).addClass("active_chat");
                    }
                    resolve();
                });
            });
        },

        formatTimestamp(timestamp) {
            let dt_ = new Date(parseInt(timestamp));
            let month = dt_.getMonth() + 1;
            let time_last_message = dt_.getHours() + ':' + (dt_.getMinutes() < 10 ? '0' : '') + dt_.getMinutes() + '  |  ' + month + '-' + dt_.getDate();
            return time_last_message;
        },
        
        scrollTopLoadMoreMessage() {
            var lastScrollTop = 0;
            var that = this;
            $('.msg_history').scroll(function() {
                var st = $(this).scrollTop();
                if (st == 0) {
                    that.getMoreMessages(that.conversation_id_now);
                }
                lastScrollTop = st;
            });
        },

        scrollBottomLoadMoreMember() {
            var lastScrollTop = 0;
            var that = this;
            $('.inbox_chat').scroll(function() {
                var st = $(this).scrollTop();
                if (st == 0) {
                    //that.getMoreMessages(that.conversation_id_now);
                }
                lastScrollTop = st;
            });
        },

        getMoreMessages(conversation_id) {
            $('.loading_image_messages').removeClass('d-none');
            $('.loading_image_messages').addClass('d-flex');
            this.limitMessage = this.limitMessage + 20;
            firebase.database().ref(`messages/${conversation_id}`)
                .off('value');
                var arr_ = [];
                firebase.database().ref(`messages/${conversation_id}`)
                    .limitToLast(this.limitMessage)
                    .on('value', (data) => {
                        let childData = data.val();
                        Object.keys(childData).map((objectKey, index) => {
                            var value = childData[objectKey];
                            var content = '';
                            var receiver_resource_text = '';
                            var receiver_resource_class = '';
                            
                            if (value.message_type == 2) {
                                content = value.content.split(","); 
                            } else {
                                content = value.content;
                            }

                            if (value.receiver_resource_action == 0) {
                                receiver_resource_text = 'CHƯA XÁC NHẬN'; 
                                receiver_resource_class = 'text-primary';

                            } else if (value.receiver_resource_action == 1) {
                                receiver_resource_text = 'ĐÃ ĐỒNG Ý'; 
                                receiver_resource_class = 'text-success';

                            } else {
                                receiver_resource_text = 'ĐÃ TỪ CHỐI'; 
                                receiver_resource_class = 'text-danger';
                            }
                            arr_.push({
                                conversation_id: conversation_id,
                                time_chat: this.formatTimestamp(value.timestamp),
                                message: content,
                                message_type: value.message_type,
                                from_user_id: value.from_user_id,
                                receiver_seen: value.receiver_seen,
                                receiver_resource_class: receiver_resource_class,
                                receiver_resource_action: receiver_resource_text,
                            });
                    });
                });
            setTimeout(() => {
                this.messages = arr_;
                $('.loading_image_messages').removeClass('d-flex');
                $('.loading_image_messages').addClass('d-none');
            }, 2500);
        }
    }
});
