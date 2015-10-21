<?php
$xPoller2 = $modx->getService('xpoller2','xPoller2',$modx->getOption('xpoller2_core_path',null,$modx->getOption('core_path').'components/xpoller2/').'model/xpoller2/',$scriptProperties);
if (!($xPoller2 instanceof xPoller2)) return '';
$modx->regClientScript($modx->getOption('xpoller2_assets_url').'js/web/default.js');
$modx->lexicon->load('xpoller2:translations');

if (empty($formOuterTpl)) {$formOuterTpl = "tpl.xPoller2.form.outer";}
if (empty($resultOuterTpl)) {$resultOuterTpl = "tpl.xPoller2.result.outer";}
if (empty($optionTpl)) {$optionTpl = "tpl.xPoller2.option";}
if (empty($resultTpl)) {$resultTpl = "tpl.xPoller2.result";}
if (empty($outputSeparator)) {$resultTpl = "\n";}
if (empty($id)) {
	$q = $modx->newQuery( "xpQuestion" );
	$q->sortby('id','DESC');
	$q->limit(1);
	$q->select( array( "id" ) );
	$s = $q->prepare(); //print $q->toSQL(); die;
	$s->execute();
	$idArray = $s->fetch(PDO::FETCH_ASSOC);
	if(!empty($idArray)) {
		$id = $idArray['id'];
	} else {
		return $modx->lexicon("xpoller2_question_err_ns");
	}
}



if ($_REQUEST['qid'] && $_REQUEST['qid'] != $id) return '';

$params = $_GET;
unset($params[$modx->getOption('request_param_alias')]);
unset($params[$modx->getOption('request_param_id')]);
if (!$modx->user->isAuthenticated($modx->context->key)) {
    $uid = 0;
} else {
    $uid = $modx->user->id;
}
$uip = $_SERVER["REMOTE_ADDR"]; 
$abstain = false;
function setxPoller2Cookie() {
	$xpVotedString = "";
	if(isset($_COOKIE['xpVoted'])) $xpVotedString = $_COOKIE['xpVoted'];
    $xpVotedString .= $_REQUEST['qid'] . ',';
    setCookie('xpVoted', $xpVotedString, time()+360000000, '/');
    
    return true;
}

if (!empty($_REQUEST['xp_action']) && $_REQUEST['qid']) {
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

    if ($_REQUEST['xp_action'] == 'abstain') {
        $abstain = true;
    	setxPoller2Cookie();
    } else {
        if ($_REQUEST['oid']) {
            $tmp = array('qid' => $id, 'uip' => $uip);
            if (!$modx->getObject('xpAnswer', $tmp)) {
                $tmp['oid'] = $_REQUEST['oid'];
                $answer = $modx->newObject('xpAnswer', $tmp);
                $answer->save();
                unset($tmp);
            }
            setxPoller2Cookie();
        }
    }
    unset($params['qid']);
    unset($params['oid']);
    unset($params['uid']);
    unset($params['uip']);
    unset($params['xp_action']);
    if (!$isAjax && empty($placeholders['message'])) {
        $modx->sendRedirect($modx->makeUrl($modx->resource->id, $modx->context->key, $params, 'full'));
    }
}



if ($modx->getObject('xpAnswer', array('uip' => $uip, 'qid' => $id)) || in_array($id, explode(",", $_COOKIE['xpVoted'])) || $abstain == true) {
    $tpl = $resultTpl;
    $outerTpl = $resultOuterTpl;
} else {
    $tpl = $optionTpl;
    $outerTpl = $formOuterTpl;
}

$q = $modx->newQuery('xpOption');
$q->where(array('qid' => $id));
$q->select('`xpOption`.`id`, `xpOption`.`qid`, `xpOption`.`option`, `xpOption`.`rank`,
            `xpOption`.`right`, `xpQuestion`.`text`, COUNT(`xpAnswer`.`uid`) as `votes`');
$q->leftJoin('xpQuestion', 'xpQuestion', array('`xpOption`.`qid` = `xpQuestion`.`id`'));
$q->leftJoin('xpAnswer',   'xpAnswer',   array('`xpAnswer`.`oid` = `xpOption`.`id`'));
$q->groupby('`xpOption`.`id`');
$q->sortby('`xpOption`.`id`', 'ASC');
$q->prepare();
/*
print "<pre>";
print $q->toSQL();
print "</pre>"; */
$q->stmt->execute();
$options = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
// print "<pre>";
// print_r($options); 
// print "</pre>";
if ($options) {
    $output = array();
    foreach ($options as $option) {
        if (empty($output['maxVotes'])) $output['maxVotes'] = $option['votes'];
        
        if ($output['maxVotes'] < $option['votes']) $output['maxVotes'] = $option['votes'];
    }
    // if (empty($output['text'])) $output['text'] = $options[0]['text'];  // Старый вывод, выводит вопрос из базы
    if (empty($output['text'])) $output['text'] = $modx->lexicon("question_" . $options[0]['qid']);
    if (empty($output['id'])) $output['id'] = $options[0]['qid'];
    foreach ($options as $option) {
    	if($output['maxVotes'] != 0) {
    		$option['percentVotes'] = round($option['votes'] / $output['maxVotes'] * 100, 2);
    	} else {
    		$option['percentVotes'] = 0;
    	}
        $option['option'] = $modx->lexicon("option_" . $option['qid'] ."_". $option['id']);
        // print_r($option);
        $output['options'][] = $xPoller2->getChunk($tpl,$option);
    }
    $output['options'] = implode($outputSeparator, $output['options']);
    $output = $xPoller2->getChunk($outerTpl, $output);
} else {
    $output = $modx->lexicon("xpoller2_question_err_ns");
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}


if (!empty($isAjax)) {
    header('Content-type: text/html; charset=utf-8');
    @session_write_close();
    exit($output);
}
else {
    return $output;
}