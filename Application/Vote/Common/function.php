<?php
/**
 * Created by PhpStorm.
 * User: jervis
 * Date: 16/5/12
 * Time: 下午8:00
 */

/**
 * 是否能投票
 */
function checkUserIsAction($id){
    $user = user();
    $roles = M('VoteRole')->where(array('vote_id' => $id))->getField('role_id', true);
    // 党务工作者
    $partyAdm = checkPublishParameter($user['uid'], $user['branch_id']); // 支部管理员
    $groupAdm = isPartyGroupLeader($user['uid'], $user['party_group_id']); // 小组组长
    // 如果角色为空
    if (!empty($roles)) {
        //如果当前用户角色在允许投票范围内 或者 当前用户是党务工作者并且投票允许党委工作者参与
        if (in_array($user['role_id'], $roles) || ($partyAdm || $groupAdm && in_array(6, $roles)) ) {
            $lists = M('VoteLimitBranch')->where(array(
                'vote_id' => $id,
            ))->getField('branch_id',true);
            // 检测当前用户所在支部是否在允许范围内
            if (!empty($lists) && in_array($user['branch_id'], $lists)) return true;
        }
    }
    return false;
}


/**
 * 是否投票
 */
function checkActionLog($id){
    $logView = D('VoteLogView');
    $voteLogs = $logView->where(array('VoteLog.uid'=>uid(),'VoteItem.vote_id'=>$id))->group('VoteLog.id')->select();
    if(sizeof($voteLogs) > 0){
        return true;
    }
    return false;
}

/**
 * 是否监督员
 */
function checkSupervisor($id){
    $_supervisor = M('VoteSupervisor');
    $list = $_supervisor->where(array('vote_id' =>$id))->getField('uid', true);
    if (!empty($list) && in_array(uid(), $list)) return true;
    return false;
}
