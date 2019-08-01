var addUrl = __root__ + "/Admin/Test/ajaxAddSubject";
var editTextUrl = __root__ + "/Admin/Test/ajaxEditText";
var editSingleUrl = __root__ + "/Admin/Test/ajaxEditSingle";
var editChoiceUrl = __root__ + "/Admin/Test/ajaxEditChoice";
var editBlankUrl = __root__ + "/Admin/Test/ajaxEditBlank";
var editAnswerUrl = __root__ + "/Admin/Test/ajaxEditAnswer";
var editTrueFalseUrl = __root__ + "/Admin/Test/ajaxEditTrueFalse";
var removeUrl = __root__ + "/Admin/Test/ajaxRemoveSubject";
var upUrl = __root__ + "/Admin/Test/ajaxUpSubject";
var downUrl = __root__ + "/Admin/Test/ajaxDownSubject";
var insertUrl = __root__ + "/Admin/Test/ajaxInsertSubject";


contentInit();

function contentInit() {
    $('.subject-blank').find('input').attr('readonly', 'readonly');
}

var isEditing = false;
var index = 0;

function checkEditing() {
    return isEditing;
}
/**
 *
 * @param html
 * @param subject
 */
function addLastInSubjectsHtml(html, subject) {
    $('.exam_subject').append(html);
    bindHover();
    setEditingStatus(false);
    _resetPublicData(subject);

}
/**显示处理状态
 * 调用
 * 点击添加时判断
 * **/
function showAdding() {
    $.beamDialog({
        title: '提示',
        content: '请先完成正在添加的题目',
        showCloseButton: false,
        otherButtons: ["确定"],
        otherButtonStyles: ['btn-info'],
        bsModalOption: {keyboard: true},
        clickButton: function (sender, modal, index) {
            $(this).closeDialog(modal);
        }
    });
}
/****滑到最底部
 * 调用
 * 点击添加触发
 * *****/
function scrollToBottom() {
    var c = window.document.body.scrollHeight;
    window.scroll(0, c);
}

/****设置 subject 的内容
 * 调用
 * 添加后触发
 * 编辑后触发
 * *****/
function _resetPublicData(subject) {
    subject_data.put("subject-" + subject.id, subject);
}

/*****设置当前编辑状态
 * 调用
 * 点击添加时触发
 *
 * ******/
function setEditingStatus(status) {
    isEditing = status;
}

/**还原状态***/
function resetStatus(obj) {
    obj.css("border", "1px solid white");
    obj.css("padding-bottom", "30px");
    obj.attr('is-editing', "false");
    removeEditor(obj);
}

/********移动绑定*******/
bindHover();
function bindHover() {
    $('.exam_subject  .subject').hover(function () {
        if ($(this).attr('is-editing') == "false") {
            showBottomButtons($(this));
        } else {
            $(this).css("border", "1px solid #1deb17");
        }
    }, function () {
        if ($(this).attr('is-editing') == "false") {
            hideBottomButtons($(this));
        } else {
            $(this).css("border", "1px solid #1deb17");
        }

    });
}


/*****显示 html************************************/
var publicShowButtonHtml = "<div class='edit-buttons hide' > " +
    "<div class='btn-group btn-group-sm pull-right' role='group' aria-label='...'> " +
    "<button class='btn btn-default btn-append' data='#id#'><span class='glyphicon glyphicon-log-out' aria-hidden='true'>在这题后面插入</span></button>" +
    "<a class='btn btn-default btn-up' data='#id#'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span></a>" +
    "<a class='btn btn-default btn-down' data='#id#'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></span></a>" +
    "<button type='button' class='btn btn-default btn-edit' data='#id#' id='btn-edit-#id#''>编辑</button> " +
    "<button type='button' class='btn btn-default btn-delete' data='#id#'>删除</button> " +
    "</div> " +
    "</div>";


/********大标题相关的***********/

function showAddTitle(subject) {
    return "<div class='subject subject_title subject-text' is-editing='false' > " +
        "<div class='s-content'>" + showTitleIn(subject) +
        "</div> " +
        "</div> ";
}
function showTitleIn(subject) {
    var showhTitleHtml_in =
        "<div> " +
        "<p class='subject-title'>#title#</p> " +
        "</div>" +
        publicShowButtonHtml +
        "<div class='clearfix'></div>";

    showhTitleHtml_in = showhTitleHtml_in
        .replace(/#id#/gm, subject.id)
        .replace('#title#', subject.title);
       
    return showhTitleHtml_in;
}
/********大标题相关的***********/
/********单选题*************/
function showAddSingle(subject) {
    return "<div class='subject subject-single' is-editing='false'> " +
        "<div style='text-align:left;'>【单选题】</div>"+
        "<div class='s-content'>" + showSingleIn(subject) +
        "</div> " +
        "</div> ";

}
function showSingleIn(subject) {
    var showIn =
        "<div class='question'> " +
        "<span class='subject-index'></span> " +
        "<span class='subject-title'>#title#</span>" +
        "（<span class='subject-right-answer'>#right_answer_str#</span>）" +
        "（<span class='subject-score'>#score#</span>分） " +
        "</div> " +
        "<div class='options'> " +
        "<div class='radio'> " +
        "<label>A.<span class='A-name'>#A-name#</span> " +
        "</label> " +
        "</div> " +
        "<div class='radio'> " +
        "<label>B.<span class='B-name'>#B-name#</span> " +
        "</label> " +
        "</div> " +
        "<div class='radio'> " +
        "<label>C.<span class='C-name'>#C-name#</span></label> " +
        "</div> " +
        "<div class='radio'> " +
        "<label>D.<span class='D-name'>#D-name#</span> </label> " +
        "</div> " +
        "<div> " +
          "<span class='subject-title'>解析：</span>" +
          "<span class='subject-title'>#reamrk#</span>" +
        "</div>" +
        "</div>" +
        publicShowButtonHtml;

       
    var answerValue = JSON.parse(subject.content);
    var remark = subject.remark;
    if(remark == null)
    {
        remark = "";
    }
    showIn = showIn
        .replace(/#id#/gm, subject.id)
        .replace('#title#', subject.title)
        .replace('#right_answer_str#', subject.right_answer_str)
        .replace('#score#', subject.score)
        .replace('#A-name#', answerValue[0])
        .replace('#B-name#', answerValue[1])
        .replace('#C-name#', answerValue[2])
        .replace('#D-name#', answerValue[3])
        .replace('#reamrk#', remark);


    return showIn;
}
/********单选题END*************/
/********判断题****************/
function showAddTrueFalse(subject) {
    return "<div class='subject subject-true-false' is-editing='false'> " +
        "<div class='s-content'>" + showTrueFalseIn(subject) +
        "</div> " +
        "</div> ";

}
function showTrueFalseIn(subject) {
    var showIn =
        "<div class='question'> " +
        "<span class='subject-index'></span> " +
        "<span class='subject-title'>#title#</span>" +
        "（<span class='subject-right-answer'>#right_answer_str#</span>）" +
        "（<span class='subject-score'>#score#</span>分） " +
        "</div> " +
        "<div class='options'> " +
        "<div class='radio'> " +
        "<label><span class='A-name'>#A-name#</span> " +
        "</label> " +
        "</div> " +
        "<div class='radio'> " +
        "<label><span class='B-name'>#B-name#</span> " +
        "</label> " +
        "</div> " +
        "<div> " +
            //"<span class='subject-title'>解析：</span>" +
            //"<span class='subject-title'>#reamrk#</span>" +
        "</div>" +
        "</div>" +
        publicShowButtonHtml;
    var answerValue = JSON.parse(subject.content);
    var remark = subject.remark;
    if(remark == null)
    {
        remark = "";
    }
    showIn = showIn
        .replace(/#id#/gm, subject.id)
        .replace('#title#', subject.title)
        .replace('#right_answer_str#', subject.right_answer_str)
        .replace('#score#', subject.score)
        .replace('#A-name#', answerValue[0])
        .replace('#B-name#', answerValue[1])
        .replace('#reamrk#', remark);


    return showIn;
}
/********判断题END*************/

/********多选题*************/
function showAddChoice(subject) {
    return "<div class='subject subject-choice' is-editing='false'> " +
    "<div style='text-align:left;'>【多选题】</div>"+
        "<div class='s-content'>" + showChoiceIn(subject) +
        "</div> " +
        "</div> ";

}
function showChoiceIn(subject) {
    var showIn =
        "<div class='question'> " +
        "<span class='subject-index'></span> " +
        "<span class='subject-title'>#title#</span>" +
        "（<span class='subject-right-answer'>#right_answer_str#</span>）" +
        "（<span class='subject-score'>#score#</span>分） " +
        "</div> " +
        "<div class='options'> " +
        "<div class='radio'> " +
        "<label>A.<span class='A-name'>#A-name#</span> " +
        "</label> " +
        "</div> " +
        "<div class='radio'> " +
        "<label>B.<span class='B-name'>#B-name#</span> " +
        "</label> " +
        "</div> " +
        "<div class='radio'> " +
        "<label>C.<span class='C-name'>#C-name#</span></label> " +
        "</div> " +
        "<div class='radio'> " +
        "<label>D.<span class='D-name'>#D-name#</span> </label> " +
        "</div> " +

        "<div class='radio'> " +
        "<label>E.<span class='E-name'>#E-name#</span> </label> " +
        "</div> " +
        "<div> " +
        "<span class='subject-title'>解析：</span>" +
        "<span class='subject-title'>#remark#</span>" +
        "</div>" +
        "</div>" +
        publicShowButtonHtml;

    var answerValue = JSON.parse(subject.content);
  //  console.log(answerValue)
    var remark = subject.remark;
    if(remark == null)
    {
        remark = "";
    }
    showIn = showIn
        .replace(/#id#/gm, subject.id)
        .replace('#title#', subject.title)
        .replace('#right_answer_str#', subject.right_answer_str)
        .replace('#score#', subject.score)
        .replace('#A-name#', answerValue[0])
        .replace('#B-name#', answerValue[1])
        .replace('#C-name#', answerValue[2])
        .replace('#D-name#', answerValue[3])
        .replace('#remark#', remark);
       //  console.log(answerValue[4])
    if(answerValue[4] != undefined){
        
        showIn = showIn.replace('#E-name#', answerValue[4])
    }else{
        
        showIn = showIn.replace("<div class='radio'> <label>E.<span class='E-name'>#E-name#</span> </label> </div>", '')
    }
    return showIn;
}
/********多选题END*************/

/********填空题*************/
function showAddBlank(subject) {
    return "<div class='subject subject-blank form-inline' is-editing='false'> " +
    "<div style='text-align:left;'>【填空题】</div>"+
        "<div class='s-content'>" + showBlankIn(subject) +
        "</div> " +
        "</div> ";

}
function showBlankIn(subject) {
      
    var showIn =
        "<div class='question'> " +
        "<span class='subject-index'></span> " +
        "<div class='subject-title' style='display: inline'>#content#</div>" +
        "（<span class='subject-score'>#score#</span>分）" +
        "</div> " +
        "<div style='text-align: left'> " +
        "<div >" +
        "<span class='subject-title'>解析：</span>" +
        "<span class='subject-title'>#remark#</span>" +
        "</div>" +
        "<div class='options'></div>" +
        "</div>"+
        publicShowButtonHtml;
    var remark = subject.remark;
    if(remark == null) {
        remark = "";
    }
    showIn = showIn
        .replace(/#id#/gm, subject.id)
        .replace('#content#', subject.show_content)
        .replace('#score#', subject.score * subject.answer_count)
        .replace('#remark#', remark);
        // console.log(showIn)
    return showIn;
}
/********填空题END*************/


/********简答题*************/
function showAddAnswer(subject) {
    return "<div class='subject subject-blank form-inline' is-editing='false'> " +
        "<div class='s-content'>" + showAnswerIn(subject) +
        "</div> " +
        "</div>";

}
function showAnswerIn(subject) {
    var showIn =

        "<div class='question'> " +
        "<span class='subject-index'></span> " +
        "<div class='subject-title' style='display: inline'>#content#</div>" +
        "（<span class='subject-score'>#score#</span>分） " +
        "</div> " +
        "<div style='text-align: left'> " +
        //"<div >解析：</div>" +
        //"<div >#reamrk#</div>" +
        "</div>" +
        "<div class='options'></div>" +
        publicShowButtonHtml;
    var remark = subject.remark;
    if(remark == null) {
        remark = "";
    }
    showIn = showIn
        .replace(/#id#/gm, subject.id)
        .replace('#content#', subject.content)
        .replace('#score#', subject.score)
        .replace('#reamrk#', remark);

    return showIn;
}
/********简答题END*************/
/*****显示 html************************************/


/*********编辑显示 html**********************/
var editPublicButtonHtml =
    "<div class='editor-bottom'>" +
    "<button class='btn btn-success btn-sm btn-edit-finish' type='button'>完成编辑</button> " +
    "<button class='btn btn-danger btn-sm btn-edit-cancel' type='button'>取消</button> " +
    "</div> ";
var editPublicScoreHtml =
    "<span class='score' style='margin-left: 10px'>分数：</span> " +
    "<select name='score' class='form-control' > " +
    "<option value='1' #score-1#>1分</option> " +
    "<option value='2' #score-2#>2分</option> " +
    "<option value='3' #score-3#>3分</option> " +
    "<option value='4' #score-4#>4分</option> " +
    "<option value='5' #score-5#>5分</option> " +
    "<option value='6' #score-6#>6分</option> " +
    "<option value='7' #score-7#>7分</option> " +
    "<option value='8' #score-8#>8分</option> " +
    "<option value='9' #score-9#>9分</option> " +
    "<option value='10' #score-10#>10分</option> " +
    "<option value='11' #score-11#>11分</option> " +
    "<option value='12' #score-12#>12分</option> " +
    "<option value='13' #score-13#>13分</option> " +
    "<option value='14' #score-14#>14分</option> " +
    "<option value='15' #score-15#>15分</option> " +
    "</select> ";
var editBlankScoreHtml =
    "<span class='score' style='margin-left: 10px'>每个填空：</span> " +
    "<select name='score' class='form-control' > " +
    "<option value='1' #score-1#>1分</option> " +
    "<option value='2' #score-2#>2分</option> " +
    "<option value='3' #score-3#>3分</option> " +
    "<option value='4' #score-4#>4分</option> " +
    "<option value='5' #score-5#>5分</option> " +
    "<option value='6' #score-6#>6分</option> " +
    "<option value='7' #score-7#>7分</option> " +
    "<option value='8' #score-8#>8分</option> " +
    "<option value='9' #score-9#>9分</option> " +
    "<option value='10' #score-10#>10分</option> " +
    "<option value='11' #score-11#>11分</option> " +
    "<option value='12' #score-12#>12分</option> " +
    "<option value='13' #score-13#>13分</option> " +
    "<option value='14' #score-14#>14分</option> " +
    "<option value='15' #score-15#>15分</option> " +
    "</select> ";
var editAnswerScoreHtml =
    "<span class='score' style='margin-left: 10px'>每个填空：</span> " +
    "<select name='score' class='form-control' > " +
    "<option value='1' #score-1#>1分</option> " +
    "<option value='2' #score-2#>2分</option> " +
    "<option value='3' #score-3#>3分</option> " +
    "<option value='4' #score-4#>4分</option> " +
    "<option value='5' #score-5#>5分</option> " +
    "<option value='6' #score-6#>6分</option> " +
    "<option value='7' #score-7#>7分</option> " +
    "<option value='8' #score-8#>8分</option> " +
    "<option value='9' #score-9#>9分</option> " +
    "<option value='10' #score-10#>10分</option> " +
    "<option value='11' #score-11#>11分</option> " +
    "<option value='12' #score-12#>12分</option> " +
    "<option value='13' #score-13#>13分</option> " +
    "<option value='14' #score-14#>14分</option> " +
    "<option value='15' #score-15#>15分</option> " +
    "<option value='16' #score-15#>16分</option> " +
    "<option value='17' #score-17#>17分</option> " +
    "<option value='18' #score-18#>18分</option> " +
    "<option value='19' #score-19#>19分</option> " +
    "<option value='20' #score-20#>20分</option> " +
    "<option value='21' #score-21#>21分</option> " +
    "<option value='22' #score-22#>22分</option> " +
    "<option value='23' #score-23#>23分</option> " +
    "<option value='24' #score-24#>24分</option> " +
    "<option value='25' #score-25#>25分</option> " +
    "<option value='26' #score-26#>26分</option> " +
    "<option value='27' #score-27#>27分</option> " +
    "<option value='28' #score-28#>28分</option> " +
    "<option value='29' #score-29#>29分</option> " +
    "<option value='30' #score-30#>30分</option> " +
    "</select> ";


var editTitleHtml =
    "<form  class='editor-subject2 form-inline alert alert-warning'> " +
    "<input name='id' type='hidden' value='#id#'>" +
    "<div class='editor-content'>" +
    "<div class='question'>" +
    "【大标题】<input class='subject-title form-control' style='width: 60%' placeholder='请输入大标题描述' name='title' value='#title#'/> " +
    "</div> " +
    "</div> " +
    editPublicButtonHtml +
    "</form>";
var editTrueFalseHtml =
    "<form  class='editor-subject2 form-inline  alert alert-warning'> " +
    "<input name='id' type='hidden' value='#id#'> " +
    "<div class='editor-content'> " +
    "<div class='question'>" +
    "<div class='editor-content-top'>" +
    "【判断题】" +
    editPublicScoreHtml +
    "</div> " +
    "<h5 style='text-align: left'>问题：</h5>" +
    "<div id='true-false-editor-div-#id#' data-status='off' class='editor-content-div'> " +
    "<script type='text/plain' id='editor-true-false-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:100%'></script> " +
    "<button class='btn btn-info btn-xs image-insert' type='button' id='true-false-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    "<textarea  class='hide' placeholder='问题' name='title' value='#title#'></textarea> " +
    "<textarea  class='hide' placeholder='remark' name='remark' value='#remark#'></textarea> " +
    "</div> " +
    "<div class='options noline'> " +
    "<div class='radio'> " +
    "<label>" +

    "<input placeholder='答案A' name='choice[]' class='form-control' value='正确' type='hidden'> " +
    "<label><input type='radio' name='is_answer' value='0' #is_answer0#>正确</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "<input placeholder='答案B' name='choice[]' class='form-control'value='错误' type='hidden'> " +
    "<label><input type='radio' name='is_answer' value='1' #is_answer1#>错误</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "</div> " +
    "</div> " +
        //"<h5 style='text-align: left'>解析：</h5>" +
        //"<div id='single-editor-remark-#id#' data-status='off' class='editor-content-div'> " +
        //"<script type='text/plain' id='editor-single-remark-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:930px'></script> " +
        //"<button class='btn btn-info btn-xs url-insert' type='button' id='single-remark-url-insert-#id#'>插入超链接</button> " +
        //"<button class='btn btn-info btn-xs image-insert' type='button' id='single-remark-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    "</div> " +
    editPublicButtonHtml +
    "</form>";
var editSingleHtml =
    "<form  class='editor-subject2 form-inline  alert alert-warning'> " +
    "<input name='id' type='hidden' value='#id#'> " +
    "<div class='editor-content'> " +
    "<div class='question'>" +
    "<div class='editor-content-top'>" +
    "【单项选择题】" +
    editPublicScoreHtml +
    "</div> " +
    "<h5 style='text-align: left'>问题：</h5>" +
    "<div id='single-editor-div-#id#' data-status='off' class='editor-content-div'> " +
    "<script type='text/plain' id='editor-single-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:100%'>#title#</script> " +
    "<button class='btn btn-info btn-xs image-insert' type='button' id='single-image-insert-#id#' style='display:none'>插入图片</button> " +
    "</div> " +
    "<textarea  class='hide' placeholder='问题' name='title' value='#title#'></textarea> " +
    "</div> " +
    "<div class='options noline'> " +
    "<div class='radio'> " +
    "<label>" +
    "A.<input placeholder='答案A' name='choice[]' class='form-control' value='#choiceA#'> " +
    "<label><input type='radio' name='is_answer' value='0' #is_answer0#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "B.<input placeholder='答案B' name='choice[]' class='form-control'value='#choiceB#'> " +
    "<label><input type='radio' name='is_answer' value='1' #is_answer1#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "C.<input placeholder='答案C' name='choice[]' class='form-control'value='#choiceC#'> " +
    "<label><input type='radio' name='is_answer' value='2' #is_answer2#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "D.<input placeholder='答案D' name='choice[]' class='form-control'value='#choiceD#'> " +
    "<label><input type='radio' name='is_answer' value='3' #is_answer3#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "<div style='text-align: left;margin-top: 10px;'><label style='color: black;font-size: 14px;font-weight: normal;'>解析：</label><textarea name='remark' style='width: 98%;min-height: 70px;padding: 10px;color: #666;display: block;'>#remark#</textarea></div>" +
   "</div> " +
    //"<h5 style='text-align: left'>解析：</h5>" +
    //"<div id='single-editor-remark-#id#' data-status='off' class='editor-content-div'> " +
    //"<script type='text/plain' id='editor-single-remark-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:930px'></script> " +
    //"<button class='btn btn-info btn-xs url-insert' type='button' id='single-remark-url-insert-#id#'>插入超链接</button> " +
    //"<button class='btn btn-info btn-xs image-insert' type='button' id='single-remark-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    "</div> " +
    editPublicButtonHtml +
    "</form>";

var editChoiceHtml =
    "<form  class='editor-subject2 form-inline  alert alert-warning'> " +
    "<input name='id' type='hidden' value='#id#'> " +
    "<div class='editor-content'> " +
    "<div class='question'>" +
    "<div class='editor-content-top'>" +
    "【多项项选择题】" +
    editPublicScoreHtml +
    "</div> " +
    "<h5 style='text-align: left'>问题：</h5>" +
    "<div id='choice-editor-div-#id#' data-status='off' class='editor-content-div'> " +
    "<script type='text/plain' id='editor-choice-#id#' style='text-align: left; border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;padding:5px;width:100%'></script> " +
    "<button class='btn btn-info btn-xs image-insert' type='button' id='choice-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    "<textarea  class='hide' placeholder='问题' name='title' value='#title#'></textarea> " +
    "<textarea  class='hide' placeholder='remark' name='remark' value='#remark#'></textarea> " +
    "</div> " +
    "<div class='options noline'> " +
    "<div class='radio'> " +
    "<label>" +
    "A.<input placeholder='答案A' name='choice[]' class='form-control' value='#choiceA#'> " +
    "<label><input type='checkbox' name='is_answer[]' value='0' #is_answer0#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "B.<input placeholder='答案B' name='choice[]' class='form-control'value='#choiceB#'> " +
    "<label><input type='checkbox' name='is_answer[]' value='1' #is_answer1#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "C.<input placeholder='答案C' name='choice[]' class='form-control'value='#choiceC#'> " +
    "<label><input type='checkbox' name='is_answer[]' value='2' #is_answer2#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "<div class='radio'> " +
    "<label>" +
    "D.<input placeholder='答案D' name='choice[]' class='form-control'value='#choiceD#'> " +
    "<label><input type='checkbox' name='is_answer[]' value='3' #is_answer3#>是否答案</label> " +
    "</label> " +
    "</div> " +

    "<div class='radio'> " +
    "<label>" +
    "E.<input placeholder='答案E' name='choice[]' class='form-control'value='#choiceE#'> " +
    "<label><input type='checkbox' name='is_answer[]' value='4' #is_answer4#>是否答案</label> " +
    "</label> " +
    "</div> " +
    "</div> " +
    "<div style='text-align: left;margin-top: 10px;'><label style='color: black;font-size: 14px;font-weight: normal;'>解析：</label><textarea name='remark' style='width: 98%;min-height: 70px;padding: 10px;color: #666;display: block;'>${remark}</textarea></div>" +
    //"<h5 style='text-align: left'>解析：</h5>" +
    //"<div id='choice-editor-remark-#id#' data-status='off' class='editor-content-div'> " +
    //"<script type='text/plain' id='editor-choice-remark-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:930px'></script> " +
    //"<button class='btn btn-info btn-xs url-insert' type='button' id='choice-remark-url-insert-#id#'>插入超链接</button> " +
    // "<button class='btn btn-info btn-xs image-insert' type='button' id='choice-remark-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    "</div> " +
    editPublicButtonHtml +
    "</form>";

var editBlankHtml =
    "<form  class='editor-subject2 form-inline  alert alert-warning'> " +
    "<input name='id' type='hidden' value='#id#'> " +
    "<div class='editor-content noline'> " +
    "<div class='editor-content-top'>" +
    "【填空题】" +
    editBlankScoreHtml +
    "</div> " +
    "<h5 style='text-align: left'>问题：</h5>" +
    "<div id='blank-editor-div-#id#' data-status='off' class='editor-content-div'> " +
    "<script type='text/plain' id='editor-blank-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;padding:5px;width:100%'></script> " +
    "<a class='btn btn-success btn-xs blank-insert' type='button' id='blank-insert-#id#' style=''>插入填空</a> " +
    "<a class='btn btn-info btn-xs image-insert' type='button' id='blank-image-insert-#id#' style=''>插入图片</a> " +
    "</div> " +
    "<div style='text-align: left;margin-top: 10px;'><label style='color: black;font-size: 14px;font-weight: normal;'>解析：</label><textarea name='remark' style='width: 98%;min-height: 70px;padding: 10px;color: #666;display: block;'>#remark#</textarea></div>" +
    //"<h5 style='text-align: left'>解析：</h5>" +
    //"<div id='blank-editor-remark-#id#' data-status='off' class='editor-content-div'> " +
    //"<script type='text/plain' id='editor-blank-remark-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:930px'></script> " +
    //"<button class='btn btn-info btn-xs url-insert' type='button' id='blank-remark-url-insert-#id#'>插入超链接</button> " +
    //"<button class='btn btn-info btn-xs image-insert' type='button' id='blank-remark-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    "</div>" +
    editPublicButtonHtml +
    "</form>";

var editAnswerHtml =
    "<form  class='editor-subject2 form-inline  alert alert-warning'> " +
    "<input name='id' type='hidden' value='#id#'> " +
    "<div class='editor-content noline'> " +
    "<div class='editor-content-top'>" +
    "【简答题】" +
    editAnswerScoreHtml +
    "</div> " +
    "<h5 style='text-align: left'>问题：</h5>" +
    "<div id='answer-editor-div-#id#' data-status='off' class='editor-content-div'> " +
    "<script type='text/plain' id='editor-answer-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:100%'></script> " +
    "<button class='btn btn-info btn-xs image-insert' type='button' id='answer-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    //"<h5 style='text-align: left'>解析：</h5>" +
    //"<div id='answer-editor-remark-#id#' data-status='off' class='editor-content-div'> " +
    //"<script type='text/plain' id='editor-answer-remark-#id#' style='text-align: left;    border: 1px solid #d4d4d4; border-radius: 4px;background-color:white;;padding:5px;width:930px'></script> " +
    //"<button class='btn btn-info btn-xs url-insert' type='button' id='answer-remark-url-insert-#id#'>插入超链接</button> " +
    //"<button class='btn btn-info btn-xs image-insert' type='button' id='answer-remark-image-insert-#id#'>插入图片</button> " +
    "</div> " +
    "</div>" +
    editPublicButtonHtml +
    "</form>";
/*********编辑显示 html**********************/
