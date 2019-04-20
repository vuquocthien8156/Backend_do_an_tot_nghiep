'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#chat-user',
    components: { Pagination },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            admin_id: null,
            path_avatar_image_now : '/images/user.png',
            path_resouce: '',
            chats: [], // list conversations
            style_Image_Chat: { 'margin-left' : '79%' },
            messages: [], // list messages
            list_conversation_id: [], //list id conversation check db and get avatar users
            chats_new_user : [], //conversation temp for new user
            arr_image_str: [],
            conversation_id_now: null, // var id conversation current
            user_id_no_hash_conversation: false,
            user_name_no_hash_conversation: '',
            id_conversation_new_for_user: null,
            limitMessage: 20,
            conversation_has_deleted: false,
        };
    },
    created() {
        this.admin_id = $('#image_upload').data('id_admin');
        this.path_resouce = $('#path_resouce').val();
        this.chatsByUser().then(() => {
            this.conversation_id_now = this.chats[0].conversation_id;
            this.getMessageByConversation(this.conversation_id_now).then(() => {
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
            this.checkConversationDetete(this.conversation_id_now);
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
        this.numberMessageNotSeen();
        this.nameConversation();
        this.updateTextColorNameCoversation();
        
    },
    mounted() {
        this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');
        $('.write_msg').on("keydown", (event) => {
            if(event.which === 13) {
                this.sendMessage();
            }
        });
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
    updated() {
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
        this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');

        $(".chat_list").on("click", function() {
            $(".chat_list").removeClass("active_chat");
            $(this).addClass("active_chat");
            this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');
        });
        this.nameConversation();
    },
    methods: {
        chatsByUser() {
            return new Promise((resolve) => {
                common.checkFirebaseReady().then(() => {
                    firebase.database().ref(`chats_by_user/u${this.admin_id}/_conversation`)
                        .orderByChild('deleted__last_updated_at')
                        .startAt(`1_`)
                        .endAt(`1_\uf8ff`)
                        .limitToLast(100)
                        .on('value', (data) => {
                            let arr = [];
                            data.forEach((child) => {
                                if (child.val().deleted == false) {
                                    let last_message = ( child.val().last_messages.message_type == 2) ? "[Hình ảnh]" : child.val().last_messages.content
                                    arr.splice(0, 0, {
                                        conversation_id: child.key,
                                        last_messages: last_message,
                                        time_last_message: this.formatTimestamp(child.val().last_messages.timestamp),
                                        last_updated_at: this.formatTimestamp(child.val().last_updated_at),
                                        avatar_path: null,
                                        name_conversation: null,
                                        number_of_unseen_messages_admin: 0,
                                        id_user_firebase : null,
                                    });
                                    this.list_conversation_id.push({
                                        conversation_id: child.key,
                                    });
                                    resolve();
                                }
                            });
                            this.chats = arr;
                        });
                    })
                })
        },
        getMessageByConversation(conversation_id, tmp) {
            this.limitMessage = 20;
            firebase.database().ref(`messages_by_conversation/${conversation_id}`).off('value');
            if (tmp == false) {
                this.messages = [];
                return true;
            }
            this.chats_new_user = [];
            this.conversation_id_now = conversation_id;
            this.user_id_no_hash_conversation = false;
            this.user_name_no_hash_conversation = null;
            return new Promise((resolve) => {
                firebase.database().ref(`messages_by_conversation/${conversation_id}`)
                    .orderByKey()
                    .limitToLast(this.limitMessage)
                    .on('value', (data) => {
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
                    $('.msg_history').scrollTop = $('.msg_history').scrollHeight;
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
                        firebase.database().ref(`members/${conversation_id}/u${this.admin_id}`).update({
                            number_of_unseen_messages: 0,
                        });
                    }, 3000);
                });
        },

        nameConversation() {
            common.checkFirebaseReady().then(() => {
                firebase.database().ref(`members`)
                    .on('value', (data) => {
                    let childData = data.val();
                    Object.keys(childData).map((objectKey, index) => {
                        var value = childData[objectKey];
                        Object.keys(value).forEach((element, index2) => {
                            if (element == `u${this.admin_id}`) {
                                $("#name_conversation_" + objectKey).text(value[element].name);
                            }
                        });
                    })
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
                        this.conversation_id_now = null;
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
                        this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');
                        this.messages = [];
                    } else {
                        let conversation_id_firebase = `c${response.info_conversation[0].conversation_id}`;
                        this.getMessageByConversation(conversation_id_firebase, true);
                        $(".chat_list").removeClass("active_chat");
                        $('#'+conversation_id_firebase).addClass("active_chat");
                        this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');
                    }
                    resolve();
                });
            });
        },
        
        sendMessage() {
            let date_ = new Date().getTime();
            let admin_id = $('#image_upload').data('id_admin');
            let name_admin = $('#image_upload').data('name_admin');
            let content_chat = $('#content_chat').val();
            let message_type = 1;
            console.log(this.messages);
            if(content_chat == "" || admin_id == "") {
                return false;
            }
            if (this.user_id_no_hash_conversation != false) {
                this.createConversationInDB(this.user_id_no_hash_conversation, this.user_name_no_hash_conversation).then(() => {
                    this.createNodeChatInFirebase(this.id_conversation_new_for_user, content_chat, message_type, admin_id, this.user_id_no_hash_conversation, this.user_name_no_hash_conversation, name_admin, date_).then(() => {
                        this.chats_new_user = [];
                        $('#content_chat').val('');
                        $(".chat_list").removeClass("active_chat");
                        setTimeout(() => {
                            $('#c' + this.id_conversation_new_for_user).addClass("active_chat");
                            this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');
                        }, 800)
                        this.getMessageByConversation(`c${this.id_conversation_new_for_user}`, true);
                        this.getMembers(`c${this.id_conversation_new_for_user}`);
                        this.checkConversationDetete(`c${this.id_conversation_new_for_user}`)
                    });
                });
            } else {
                if(this.conversation_has_deleted == true) {
                    let name_user_now = $(".chat_list.active_chat").children().children().children().children().first().text();
                    this.checkUsersHasConversationNew(this.id_user_firebase_now, name_user_now, name_admin, content_chat, message_type, date_);
                } else {
                    this.pushContent(content_chat, admin_id, date_, message_type, this.conversation_id_now, this.id_user_firebase_now).then(() => {
                        $('#content_chat').val('');
                        $('.msg_history').animate({
                            scrollTop: $('.msg_history').get(0).scrollHeight
                        }, 800);
                    });
                }
                
            }
        },

            createNodeChatInFirebase(id_new_conversation, content_chat, message_type, admin_id, id_user, name_admin, name_user, date_) {
            return new Promise((resolve) => {
                common.checkFirebaseReady().then(() => {
                    firebase.database().ref(`messages_by_conversation/c${id_new_conversation}`).push().set({
                        content: content_chat,
                        from_user_id: admin_id,
                        message_type: message_type,
                        receiver_seen: false,
                        timestamp: date_,
                        receiver_resource_action: 0,
                    });
                    let content_chat_last_message = (message_type == 2) ? '[Hình ảnh]' : content_chat;
                    firebase.database().ref(`chats_by_user/u${this.admin_id}/_conversation/c${id_new_conversation}`).set({
                        deleted: false,
                        deleted__last_updated_at: `1_${date_}`,
                        last_updated_at: date_,
                        last_messages: {
                            content: content_chat_last_message,
                            timestamp: date_,
                            message_type: message_type,
                        }
                    });
                    firebase.database().ref(`chats_by_user/u${id_user}/_conversation/c${id_new_conversation}`).set({
                        deleted: false,
                        deleted__last_updated_at: `1_${date_}`,
                        last_updated_at: date_,
                        last_messages: {
                            content: content_chat_last_message,
                            timestamp: date_,
                            message_type: message_type,
                        }
                    });
                    firebase.database().ref(`members/c${id_new_conversation}`).set({
                        [`u${admin_id}`]: {
                            deleted_conversation: false,
                            name: name_admin,
                            number_of_unseen_messages: 0,
                        },
                        [`u${id_user}`]: {
                            deleted_conversation: false,
                            name: name_user,
                            number_of_unseen_messages: 1,
                        },
                    });
                    firebase.database().ref(`chats_by_user/u${this.admin_id}/_all_conversation`).update({
                        conversation_id: id_new_conversation,
                        last_updated_at: date_,
                        from_user_id: this.admin_id,
                        last_messages: {
                            content: content_chat_last_message,
                            timestamp: date_,
                            message_type: message_type,
                        }
                    });
                    firebase.database().ref(`chats_by_user/u${id_user}/_all_conversation`).set({
                        conversation_id: id_new_conversation,
                        last_updated_at: date_,
                        from_user_id: this.admin_id,
                        last_messages: {
                            content: content_chat_last_message,
                            timestamp: date_,
                            message_type: message_type,
                        }
                    });
                    firebase.database().ref(`conversation/c${id_new_conversation}`).set({
                        deleted: false,
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

        pushContent(content_chat, admin_id, date_, message_type, conversation_id_now, id_user_firebase) {
            return new Promise((resolve) => {
                firebase.database().ref(`messages_by_conversation/${conversation_id_now}`).push().set({
                    content: content_chat,
                    from_user_id: admin_id,
                    message_type: message_type,
                    receiver_seen: false,
                    timestamp: date_,
                    receiver_resource_action: 0,
                });
                let content_chat_last_message = (message_type == 2) ? '[Hình ảnh]' : content_chat;
                firebase.database().ref(`chats_by_user/u${this.admin_id}/_all_conversation`).update({
                    conversation_id: conversation_id_now,
                    from_user_id: this.admin_id,
                    last_updated_at: date_,
                    last_messages: {
                        content: content_chat_last_message,
                        message_type: message_type,
                        timestamp: date_,
                    }
                });
                firebase.database().ref(`chats_by_user/u${this.admin_id}/_conversation/${conversation_id_now}`).update({
                    deleted: false,
                    deleted__last_updated_at: `1_${date_}`,
                    last_updated_at: date_,
                    last_messages: {
                        content: content_chat_last_message,
                        message_type: message_type,
                        timestamp: date_,
                    }
                });
                firebase.database().ref(`chats_by_user/${id_user_firebase}/_all_conversation`).update({
                    conversation_id: conversation_id_now,
                    from_user_id: this.admin_id,
                    last_updated_at: date_,
                    last_messages: {
                        content: content_chat_last_message,
                        message_type: message_type,
                        timestamp: date_,
                    }
                });
                firebase.database().ref(`chats_by_user/${id_user_firebase}/_conversation/${conversation_id_now}`).update({
                    deleted: false,
                    deleted__last_updated_at: `1_${date_}`,
                    last_updated_at: date_,
                    last_messages: {
                        content: content_chat_last_message,
                        message_type: message_type,
                        timestamp: date_,
                    }
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
                if(this.conversation_id_now != null && this.conversation_has_deleted == false) {
                    this.pushContent(this.arr_image_str.toString(), this.admin_id, date_, message_type, this.conversation_id_now, this.id_user_firebase_now).then(() => {
                        $('#content_chat').val('');
                        $('.msg_history').animate({
                            scrollTop: $('.msg_history').get(0).scrollHeight
                        }, 800);
                        this.arr_image_str = [];
                        $('.loading_image').removeClass('d-block');
                        $('.loading_image').addClass('d-none');
                    });
                }
                $('.loading_image').removeClass('d-block');
                $('.loading_image').addClass('d-none');
            });
        },

        scrollTopLoadMoreMessage() {
            var lastScrollTop = 0;
            var that = this;
            $('.msg_history').scroll(function() {
                var st = $(this).scrollTop();
                if (st == 0 && that.conversation_id_now != null) {
                    that.getMoreMessages(that.conversation_id_now);
                }
                lastScrollTop = st;
            });
        },

        getMoreMessages(conversation_id) {
            $('.loading_image_messages').removeClass('d-none');
            $('.loading_image_messages').addClass('d-flex');
            this.limitMessage = this.limitMessage + 20;
            firebase.database().ref(`messages_by_conversation/${conversation_id}`).off('value');
                var arr_ = [];
                firebase.database().ref(`messages_by_conversation/${conversation_id}`)
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
        },

        formatTimestamp(timestamp) {
            let dt_ = new Date(parseInt(timestamp));
            let month = dt_.getMonth() + 1;
            let time_last_message = dt_.getHours() + ':' + (dt_.getMinutes() < 10 ? '0' : '') + dt_.getMinutes() + '  |  ' + (month < 10 ? '0' : '') + month + '-' + (dt_.getDate() < 10 ? '0' : '') + dt_.getDate() + '-' + (dt_.getFullYear() + " ").substr(2);
            return time_last_message;
        },

        checkConversationDetete(conversation_id) {
            common.checkFirebaseReady().then(() => {
                firebase.database().ref(`conversation/${conversation_id}`)
                    .on('value', (data) => {
                    if(data.val().deleted == true) {
                        this.conversation_has_deleted = true;
                    } else {
                        this.conversation_has_deleted = false;
                    }
                });
            });
        },

        checkUsersHasConversationNew(user_id, name_user_now, name_admin, content_chat, message_type, date_) {

            return new Promise((resolve) => {
                var data = {
                    id_user: user_id.toString().replace('u', ''),
                };
                $.post('/conversation/check-user-has-conversation', data)
                .done(response => {
                    if (response.has_conversation === false) {
                        this.createConversationInDB(user_id.toString().replace('u', ''), name_user_now).then(() => {
                            if(this.id_conversation_new_for_user != null) {
                                this.createNodeChatInFirebase(this.id_conversation_new_for_user, content_chat, message_type, this.admin_id, user_id.toString().replace('u', ''), name_user_now, name_admin, date_).then(() => {
                                    this.chats_new_user = [];
                                    $('#content_chat').val('');
                                    $(".chat_list").removeClass("active_chat");
                                    setTimeout(() => {
                                        $('#c' + this.id_conversation_new_for_user).addClass("active_chat");
                                        this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');
                                    }, 800)
                                    this.getMessageByConversation(`c${this.id_conversation_new_for_user}`, true);
                                    this.getMembers(`c${this.id_conversation_new_for_user}`);
                                    this.checkConversationDetete(`c${this.id_conversation_new_for_user}`);
                                });
                            }
                        });
                    } else {
                        let conversation_id_firebase = `c${response.info_conversation[0].conversation_id}`;
                        this.getMembers(`c${this.id_conversation_new_for_user}`);
                        this.pushContent(content_chat, this.admin_id, date_, message_type, conversation_id_firebase, user_id).then(() => {
                            $('#content_chat').val('');
                            $('.msg_history').animate({
                                scrollTop: $('.msg_history').get(0).scrollHeight
                            }, 800);
                        });
                        this.getMessageByConversation(conversation_id_firebase, true);
                        this.checkConversationDetete(conversation_id_firebase);
                        $(".chat_list").removeClass("active_chat");
                        $('#'+conversation_id_firebase).addClass("active_chat");
                        this.path_avatar_image_now = $(".chat_list.active_chat").children().children().children().attr('src');
                    }
                    resolve();
                });
            });
        },

        updateTextColorNameCoversation() {
            common.checkFirebaseReady().then(() => {
                firebase.database().ref(`conversation`)
                    .on('value', (data) => {
                        let childData = data.val();
                        Object.keys(childData).map((objectKey, index) => {
                            var value = childData[objectKey];
                            if (value.deleted == true) {
                                $('#name_conversation_' + objectKey).css("color", "red")
                            }
                        });
                });
            });
        }

    }
});