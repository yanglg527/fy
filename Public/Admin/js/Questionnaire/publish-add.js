/**************插入多选题********************/
$('#insert-text').click(function () {
    insertSubject( $('.exam_subject'), 0, 'text', true);
})
$('#insert-single-choice').click(function () {
    insertSubject( $('.exam_subject'), 0, 'singleChoice', true);
})
$('#insert-choice').click(function () {
    insertSubject( $('.exam_subject'), 0, 'choice', true);
})
$('#insert-blank').click(function () {
    insertSubject( $('.exam_subject'), 0, 'blank', true);
})
$('#insert-answer').click(function () {
    insertSubject( $('.exam_subject'), 0, 'answer', true);
})

$('#insert-true-false').click(function () {
    insertSubject( $('.exam_subject'), 0, 'trueFalse', true);
})