/***显示底部按钮****/
function showBottomButtons(obj) {
    $(obj).css("border", "1px solid #1deb17");
    $(obj).find('.edit-buttons').removeClass('hide');
}
/****隐藏底部按钮*****/
function hideBottomButtons(obj) {
    $(obj).find('.edit-buttons').addClass('hide');
    $(obj).css("border", "1px solid white");
}

/*****显示正在编辑*******/
function showEditing(obj) {
    hideBottomButtons(obj);

    $(obj).css("border", "1px solid #1deb17");
    $(obj).css("padding-bottom", "0px");
}


/******在这题后面插入*****/
$('.exam_subject').on('click', '.btn-append', function () {
    var id = $(this).attr('data');
    var $parent = $(this).parent().parent().parent().parent();
    new $.flavr({
        buttonDisplay: 'stacked',
        content: 'flavr with stacked buttons',
        buttons: {
            '插入大标题': {
                style: 'info',
                action: function () {
                    insertSubject($parent, id, 'text',false);
                    return true
                }
            },
            '插入单选题': {
                style: 'default',
                action: function () {
                    insertSubject($parent, id, 'singleChoice',false);
                    return true
                }
            },
            '插入多选题': {
                style: 'default',
                action: function () {
                    insertSubject($parent, id, 'choice',false);
                    return true
                }
            },
            '插入判断题目': {
                style: 'default',
                action: function () {
                    insertSubject($parent, id, 'trueFalse',false);
                    return true
                }
            },

            '关闭': {style: 'danger'},
        }
    });

    //'插入填空题': {
    //    style: 'default',
    //        action: function () {
    //        insertSubject($parent, id, 'blank',false);
    //        return true
    //    }
    //},
    //'插入简答题': {
    //    style: 'default',
    //        action: function () {
    //        insertSubject($parent, id, 'answer',false);
    //        return true
    //    }
    //},


})

function insertSubject($parent, id, type,is_add) {
    //alert(id + "--" + type);
    if(is_add){
        is_add = true;
    }else{
        is_add = false;
    }
   
    $.ajax({
        url: insertUrl,
        type: "post",
        data: {
            is_add:is_add,
            exam_id:examId,
            id: id,
            type: type
        },
        dataType: "json",
        success: function (data) {
            if (data.status == 0) {
                if(is_add){
                    if (type == 'text') {
                        appendAddContentHtml($parent, showAddTitle(data.data), data.data);
                    } else if (type == 'singleChoice') {
                        appendAddContentHtml($parent, showAddSingle(data.data), data.data);
                    } else if (type == 'trueFalse') {
                        appendAddContentHtml($parent, showAddTrueFalse(data.data), data.data);
                    } else if (type == 'choice') {
                        appendAddContentHtml($parent, showAddChoice(data.data), data.data);
                    } else if (type == 'blank') {
                        appendAddContentHtml($parent, showAddBlank(data.data), data.data);
                    } else if (type == 'answer') {
                        appendAddContentHtml($parent, showAddAnswer(data.data), data.data);
                    }
                }else{
                    if (type == 'text') {
                        appendContentHtml($parent, showAddTitle(data.data), data.data);
                    } else if (type == 'singleChoice') {
                        appendContentHtml($parent, showAddSingle(data.data), data.data);
                    } else if (type == 'trueFalse') {
                        appendContentHtml($parent, showAddTrueFalse(data.data), data.data);
                    } else if (type == 'choice') {
                        appendContentHtml($parent, showAddChoice(data.data), data.data);
                    } else if (type == 'blank') {
                        appendContentHtml($parent, showAddBlank(data.data), data.data);
                    } else if (type == 'answer') {
                        appendContentHtml($parent, showAddAnswer(data.data), data.data);
                    }
                }



            } else {
                alert(data.msg);
            }
        },
        error: function () {
            alert("服务器暂时无法访问，请稍后再试");
        }
    });
}

function insertSubject($parent, id, type,is_add) {
    //alert(id + "--" + type);
    if(is_add){
        is_add = true;
    }else{
        is_add = false;
    }

    $.ajax({
        url: insertUrl,
        type: "post",
        data: {
            is_add:is_add,
            exam_id:examId,
            id: id,
            type: type
        },
        dataType: "json",
        success: function (data) {
            if (data.status == 0) {
                if(is_add){
                    if (type == 'text') {
                        appendAddContentHtml($parent, showAddTitle(data.data), data.data);
                    } else if (type == 'singleChoice') {
                        appendAddContentHtml($parent, showAddSingle(data.data), data.data);
                    } else if (type == 'trueFalse') {
                        appendAddContentHtml($parent, showAddTrueFalse(data.data), data.data);
                    }else if (type == 'choice') {
                        appendAddContentHtml($parent, showAddChoice(data.data), data.data);
                    } else if (type == 'blank') {
                        appendAddContentHtml($parent, showAddBlank(data.data), data.data);
                    } else if (type == 'answer') {
                        appendAddContentHtml($parent, showAddAnswer(data.data), data.data);
                    }
                }else{
                    if (type == 'text') {
                        appendContentHtml($parent, showAddTitle(data.data), data.data);
                    } else if (type == 'singleChoice') {
                        appendContentHtml($parent, showAddSingle(data.data), data.data);
                    } else if (type == 'trueFalse') {
                        appendContentHtml($parent, showAddTrueFalse(data.data), data.data);
                    }else if (type == 'choice') {
                        appendContentHtml($parent, showAddChoice(data.data), data.data);
                    } else if (type == 'blank') {
                        appendContentHtml($parent, showAddBlank(data.data), data.data);
                    } else if (type == 'answer') {
                        appendContentHtml($parent, showAddAnswer(data.data), data.data);
                    }
                }



            } else {
                alert(data.msg);
            }
        },
        error: function () {
            alert("服务器暂时无法访问，请稍后再试");
        }
    });
}

/******上移*****/
$('.exam_subject').on('click', '.btn-up', function () {
    var id = $(this).attr('data');
    $.ajax({
        url: upUrl,
        type: "post",
        data: {
            id: id
        },
        dataType: "json",
        success: function (data) {
            if (data.status == 0) {
                window.location.reload();
            } else {
                alert(data.msg);
            }
        },
        error: function () {
            alert("服务器暂时无法访问，请稍后再试");
        }
    });
})


/******下移*****/
$('.exam_subject').on('click', '.btn-down', function () {
    var id = $(this).attr('data');
    $.ajax({
        url: downUrl,
        type: "post",
        data: {
            id: id
        },
        dataType: "json",
        success: function (data) {
            if (data.status == 0) {
                window.location.reload();
            } else {
                alert(data.msg);
            }
        },
        error: function () {
            alert("服务器暂时无法访问，请稍后再试");
        }
    });
})
/****点击编辑按钮******/
$('.exam_subject').on('click', '.btn-edit', function () {

    var id = $(this).attr('data');
    var subject = subject_data.get("subject-" + id);
    var parent = $(this).parent().parent().parent().parent();
    var html = "";
    if (subject.type_num == 'text') {//获取编辑大标题的 html
        html = editTitleHtml
            .replace(/#id#/gm, subject.id)
            .replace('#title#', subject.title);
    } else if (subject.type_num == 'singleChoice') {//获取编辑单选的文本
        var choice = JSON.parse(subject.content);
        var right_answer = JSON.parse(subject.right_answer);
        //解析哪个选中
        var right_index = 0;
        for (var i = 0; i < right_answer.length; i++) {
            if (right_answer[i] == 1) {
                right_index = i;
                break;
            }
        }

        html = editSingleHtml
            .replace(/#id#/gm, subject.id)
            .replace('#title#', subject.title)
            .replace('#score-' + subject.score + '#', "selected='selected'")
            .replace('#choiceA#', choice[0])
            .replace('#choiceB#', choice[1])
            .replace('#choiceC#', choice[2])
            .replace('#choiceD#', choice[3])
            .replace('#is_answer' + right_index + '#', "checked='checked'");
    } else if (subject.type_num == 'trueFalse') {//获取编辑单选的文本
        var choice = JSON.parse(subject.content);
        var right_answer = JSON.parse(subject.right_answer);
        //解析哪个选中
        var right_index = 0;
        for (var i = 0; i < right_answer.length; i++) {
            if (right_answer[i] == 1) {
                right_index = i;
                break;
            }
        }

        html = editTrueFalseHtml
            .replace(/#id#/gm, subject.id)
            .replace('#title#', subject.title)
            .replace('#score-' + subject.score + '#', "selected='selected'")
            .replace('#choiceA#', choice[0])
            .replace('#choiceB#', choice[1])
            .replace('#is_answer' + right_index + '#', "checked='checked'");
    } else if (subject.type_num == 'choice') {//获取多选的文本
        var choice = JSON.parse(subject.content);
        html = editChoiceHtml
            .replace(/#id#/gm, subject.id)
            .replace('#title#', subject.title)
            .replace('#score-' + subject.score + '#', "selected='selected'")
            .replace('#choiceA#', choice[0])
            .replace('#choiceB#', choice[1])
            .replace('#choiceC#', choice[2])
            .replace('#choiceD#', choice[3]);

        var right_answer = JSON.parse(subject.right_answer);
        //解析哪个选中
        for (var i = 0; i < right_answer.length; i++) {
            if (right_answer[i] == 1) {
                html = html.replace('#is_answer' + i + '#', "checked='checked'");

            }
        }


    } else if (subject.type_num == 'blank') {//获取填空的文本
        html = editBlankHtml
            .replace(/#id#/gm, subject.id)
            .replace('#score-' + subject.score + '#', "selected='selected'");

    } else if (subject.type_num == 'answer') {//获取简单的文本
        html = editAnswerHtml
            .replace(/#id#/gm, subject.id)
            .replace('#score-' + subject.score + '#', "selected='selected'");
    }


    if (parent.find(".editor-subject2").length > 0) {
        parent.find('.editor-subject2').removeClass('hide');
    } else {//第一次插入
        parent.append(html);

        if(subject.type_num == 'singleChoice'){
            var ueSingle = UE.getEditor('editor-single-' + subject.id);

            $('#single-image-insert-' + subject.id).click(function () {
                $('#single-editor-div-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                ueSingle.setContent(subject.title);
            }, 300);

            /***隐藏解析****
            var ueSingleRemark = UE.getEditor('editor-single-remark-' + subject.id);

            $('#single-remark-url-insert-' + subject.id).click(function () {

                $('#single-editor-remark-' + subject.id).find('.edui-for-link').find('.edui-button-body').click();
                return false;
            })

            $('#single-remark-image-insert-' + subject.id).click(function () {
                $('#single-editor-remark-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                ueSingleRemark.setContent(subject.remark);
            }, 300);
            **隐藏解析****/
        }else if(subject.type_num == 'trueFalse'){
            var ueTrueFalse = UE.getEditor('editor-true-false-' + subject.id);

            $('#true-false-image-insert-' + subject.id).click(function () {
                $('#true-false-editor-div-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                ueTrueFalse.setContent(subject.title);
            }, 300);

            /***隐藏解析****
             var ueSingleRemark = UE.getEditor('editor-single-remark-' + subject.id);

             $('#single-remark-url-insert-' + subject.id).click(function () {

                $('#single-editor-remark-' + subject.id).find('.edui-for-link').find('.edui-button-body').click();
                return false;
            })

             $('#single-remark-image-insert-' + subject.id).click(function () {
                $('#single-editor-remark-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
             setTimeout(function () {
                ueSingleRemark.setContent(subject.remark);
            }, 300);
             **隐藏解析****/
        }else if(subject.type_num == 'choice'){
            var ueChoice = UE.getEditor('editor-choice-' + subject.id);

            $('#choice-image-insert-' + subject.id).click(function () {
                $('#choice-editor-div-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                ueChoice.setContent(subject.title);
            }, 300);
            /***隐藏解析***
            var uechoiceRemark = UE.getEditor('editor-choice-remark-' + subject.id);


            $('#choice-remark-url-insert-' + subject.id).click(function () {

                $('#choice-editor-remark-' + subject.id).find('.edui-for-link').find('.edui-button-body').click();
                return false;
            })

            $('#choice-remark-image-insert-' + subject.id).click(function () {
                $('#choice-editor-remark-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                uechoiceRemark.setContent(subject.remark);
            }, 300);
             **隐藏解析****/
        }else if (subject.type_num == 'blank') {//获取填空的文本
            var ueBlank = UE.getEditor('editor-blank-' + subject.id);
            $('#blank-insert-' + subject.id).click(function () {
                //var id = $(this).attr('data');
                ueBlank.focus();
                ueBlank.execCommand('inserthtml', '<input class="input-blank form-control" value="" placeholder="请输入答案" name="subject-' + subject.id + '[]"/>')
            })

            $('#blank-image-insert-' + subject.id).click(function () {
                $('#blank-editor-div-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
            })
            setTimeout(function () {
                ueBlank.setContent(subject.content);
                //ueBlank.focus();
                //ueBlank.execCommand('inserthtml', subject.content);
            }, 300);

            /***隐藏解析***
            var ueblankRemark = UE.getEditor('editor-blank-remark-' + subject.id);

            $('#blank-remark-url-insert-' + subject.id).click(function () {

                $('#blank-editor-remark-' + subject.id).find('.edui-for-link').find('.edui-button-body').click();
                return false;
            })

            $('#blank-remark-image-insert-' + subject.id).click(function () {
                $('#blank-editor-remark-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                ueblankRemark.setContent(subject.remark);
            }, 300);
             /***隐藏解析****/
        } else if (subject.type_num == 'answer') {//获取简单的文本
            var ueAnswer = UE.getEditor('editor-answer-' + subject.id);

            $('#answer-image-insert-' + subject.id).click(function () {
                $('#answer-editor-div-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                ueAnswer.setContent(subject.content);
            }, 300);

            /***隐藏解析****
            var ueanswerRemark = UE.getEditor('editor-answer-remark-' + subject.id);

            $('#answer-remark-url-insert-' + subject.id).click(function () {

                $('#answer-editor-remark-' + subject.id).find('.edui-for-link').find('.edui-button-body').click();
                return false;
            })

            $('#answer-remark-image-insert-' + subject.id).click(function () {
                $('#answer-editor-remark-' + subject.id).find("iframe").contents().find('input[name="upfile"]').click();
                return false;
            })
            setTimeout(function () {
                ueanswerRemark.setContent(subject.remark);
            }, 300);
             /***隐藏解析****/
        }


    }
    parent.attr('is-editing', "true");
    showEditing(parent);

})
/****点击删除按钮******/
$('.exam_subject').on('click', '.btn-delete', function () {

    var parent = $(this).parent().parent().parent().parent();
    var id = $(this).attr('data');
    $.beamDialog({
        title: '提示',
        content: '删除题目后将不能恢复，是否扔要删除?',
        showCloseButton: false,
        otherButtons: ["删除", "取消"],
        otherButtonStyles: ['btn-warning', 'btn-info'],
        bsModalOption: {keyboard: true},
        clickButton: function (sender, modal, index) {
            var dialog = $(this);
            if (index == 0) {
                $.ajax({
                    url: removeUrl,
                    type: "post",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 0) {
                            dialog.closeDialog(modal);
                            parent.remove();
                        } else {
                            alert(data.msg);
                        }
                    },
                    error: function () {
                        alert("服务器暂时无法访问，请稍后再试");
                    }
                });
            } else {
                $(this).closeDialog(modal);
            }

        }
    });
})

function removeEditor(obj) {
    obj.find('.editor-subject2').addClass('hide');
}

/****点击取消按钮******/
$('.exam_subject ').on('click', '.btn-edit-cancel', function () {
    var parent = $(this).parent().parent().parent();
    var id = parent.find('input[name="id"]').val();
    var subject = subject_data.get("subject-" + id);
    var isNew = $('#btn-edit-' + subject.id).attr('is-new');
    if(isNew == 1){//判断是否新建
        $.ajax({
            url: removeUrl,
            type: "post",
            data: {
                'id':  subject.id
            },
            dataType: "json",
            success: function (data) {
                if (data.status == 0) {
                    parent.remove();
                } else {
                    alert(data.msg);
                }
            },
            error: function () {
                alert("服务器暂时无法访问，请稍后再试");
            }
        });
    }else{
        var parent = $(this).parent().parent().parent();
        resetStatus(parent);
    }

})
$('.exam_subject ').on('click', '.btn-edit-finish', function () {


    var parent = $(this).parent().parent().parent();
    var id = parent.find('input[name="id"]').val();
    var subject = subject_data.get("subject-" + id);
    $('#btn-edit-' + subject.id).attr('is-new','0');//标记不为新建

    var data = "";
    var url = "";



    if (subject.type_num == 'text') {
        url = editTextUrl;
        data = parent.find('.editor-subject2').serialize();
    } else if (subject.type_num == 'singleChoice') {
        var ue = UE.getEditor('editor-single-' + id);
        var title = ue.getContent();

        url = editSingleUrl;
        parent.find('.editor-subject2').find('textarea[name="title"]').val(title);
        //var remarkEditor = UE.getEditor('editor-single-remark-' + id);
        //parent.find('.editor-subject2').find('textarea[name="remark"]').val(remarkEditor.getContent());
        data = parent.find('.editor-subject2').serialize();

    } else if (subject.type_num == 'trueFalse') {
        var ue = UE.getEditor('editor-true-false-' + id);
        var title = ue.getContent();

        url = editTrueFalseUrl;
        parent.find('.editor-subject2').find('textarea[name="title"]').val(title);
        //var remarkEditor = UE.getEditor('editor-true-false-remark-' + id);
        //parent.find('.editor-subject2').find('textarea[name="remark"]').val(remarkEditor.getContent());
        data = parent.find('.editor-subject2').serialize();

    } else if (subject.type_num == 'choice') {
        var ue = UE.getEditor('editor-choice-' + id);
        var title = ue.getContent();
        url = editChoiceUrl;
        parent.find('.editor-subject2').find('textarea[name="title"]').val(title);
        //var remarkEditor = UE.getEditor('editor-choice-remark-' + id);
        //parent.find('.editor-subject2').find('textarea[name="remark"]').val(remarkEditor.getContent());
        data = parent.find('.editor-subject2').serialize();
    } else if (subject.type_num == 'blank') {
        url = editBlankUrl;

        var ueBlank = UE.getEditor('editor-blank-' + id);
        var content = ueBlank.getContent();
        //var remarkEditor = UE.getEditor('editor-blank-remark-' + id);
        var answerInputs = $('#blank-editor-div-' + id).find("iframe").contents().find('.input-blank');
        var answers = new Array();
        //$.each(answerInputs, function () {
        //    answers.push($(this).val());
        //    $(this).attr('value', $(this).val());
        //});


        data = {
            id: id,
            score: parent.find('select[name="score"]').val(),

            'content': ueBlank.getContent(),
            'answer': JSON.stringify(answers)
        }
        //'remark': remarkEditor.getContent(),

    } else if (subject.type_num == 'answer') {
        url = editAnswerUrl;

        //var remarkEditor = UE.getEditor('editor-answer-remark-' + id);
        var ue = UE.getEditor('editor-answer-' + id);
        var content = ue.getContent();
        data = {
            id: id,

            score: parent.find('select[name="score"]').val(),
            'content': content
        }
        //'remark': remarkEditor.getContent(),
    }


    $.ajax({
        url: url,
        type: "post",
        data: data,
        dataType: "json",
        success: function (data) {
            if (data.status == 0) {
                resetStatus(parent);
                if (subject.type_num == 'text') {
                    resetContentHtml(parent, showTitleIn(data.data), data.data);
                } else if (subject.type_num == 'singleChoice') {
                    resetContentHtml(parent, showSingleIn(data.data), data.data);
                } else if (subject.type_num == 'trueFalse') {
                    resetContentHtml(parent, showTrueFalseIn(data.data), data.data);
                } else if (subject.type_num == 'choice') {
                    resetContentHtml(parent, showChoiceIn(data.data), data.data);
                } else if (subject.type_num == 'blank') {
                    resetContentHtml(parent, showBlankIn(data.data), data.data);
                } else if (subject.type_num == 'answer') {
                    resetContentHtml(parent, showAnswerIn(data.data), data.data);
                }
            } else {
                alert(data.msg);
            }
        },
        error: function () {
            alert("服务器暂时无法访问，请稍后再试");
        }
    });
})

function resetContentHtml(parent, html, subject) {
    parent.find('.s-content').html('');
    parent.find('.s-content').html(html);
    _resetPublicData(subject);
    contentInit();
}

function appendContentHtml($parent, html, subject) {
    $parent.after(html);
    bindHover();
    _resetPublicData(subject);
    $('#btn-edit-' + subject.id).click();
    $('#btn-edit-' + subject.id).attr('is-new','1');
}

function appendAddContentHtml($parent, html, subject) {
    $parent.append(html);
    bindHover();
    _resetPublicData(subject);
    $('#btn-edit-' + subject.id).click();
    $('#btn-edit-' + subject.id).attr('is-new','1');
    scrollToBottom();
}
function appendFirstContentHtml($parent, html, subject) {
    $parent.append(html);
    bindHover();
    _resetPublicData(subject);
    scrollToBottom();
}






