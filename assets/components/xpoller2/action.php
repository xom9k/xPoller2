<?php

// >> Подключаем
define('MODX_API_MODE', true);
$current_dir = dirname(__FILE__) .'/';
$index_php = $current_dir .'index.php';
$i=0;
while( !file_exists( $index_php ) && $i < 9 )
{
	$current_dir = dirname(dirname($index_php)) .'/';
	$index_php = $current_dir .'index.php';
	$i++;
}
if( file_exists($index_php) )
{
	require_once $index_php;
}
else {
	print "Error. Dont require MODX."; die;
}
// << Подключаем
define('MODX_ACTION_MODE', true);
$Properties = $_SESSION['xPoller2'];

//print "<pre>";
//print_r($_REQUEST);
//print "</pre>";

$xPoller2 = $modx->getService('xpoller2','xPoller2',$modx->getOption('xpoller2_core_path',null,$modx->getOption('core_path').'components/xpoller2/').'model/xpoller2/',$Properties);
if (!($xPoller2 instanceof xPoller2)) return '';
$modx->lexicon->load('xpoller2:translations');

$qid = $id = $_REQUEST['qid'];
$oid = $_REQUEST['oid'];
$action = $_REQUEST['xp_action'];

$formOuterTpl 		= $_SESSION['xPoller2']['formOuterTpl'];
$resultOuterTpl 	= $_SESSION['xPoller2']['resultOuterTpl'];
$optionTpl 			= $_SESSION['xPoller2']['optionTpl'];
$resultTpl			= $_SESSION['xPoller2']['resultTpl'];
$allowGuest			= $_SESSION['xPoller2']['allowGuest'];

// if (empty($outputSeparator)) {$resultTpl = "\n";}


$params = array();

if (!$modx->user->isAuthenticated($modx->context->key)) {
    $uid = 0;
    if($allowGuest == 0 ) $hideForm = true;
} else {
    $uid = $modx->user->id;
}
$uip = $_SERVER["REMOTE_ADDR"]; 
$abstain = false;

if ($action == 'abstain') {
    $abstain = true;
    //$xPoller2->setxPoller2Cookie($qid);
} else {
    if ($oid) {
    	foreach($oid as $oVal) {
    		$tmp = array('qid' => $id, 'uip' => $uip, 'uid' => $uid, 'oid' => $oVal);
	        if (!$modx->getObject('xpAnswer', $tmp)) {
	            //print $tmp['oid'];
	            $answer = $modx->newObject('xpAnswer', $tmp);
	            $answer->save();
	        }
	        unset($tmp);
    	}
        //$xPoller2->setxPoller2Cookie($qid);
    }
}
unset($params['qid']);
unset($params['oid']);
unset($params['uid']);
unset($params['uip']);
unset($params['xp_action']);

if (!$modx->user->isAuthenticated($modx->context->key)) {
    if ($modx->getObject('xpAnswer', array('uip' => $uip, 'qid' => $id)) || in_array($id, explode(",", $_COOKIE['xpVoted'])) || $abstain == true || $hideForm == true) {
        $tpl = $resultTpl;
        $outerTpl = $resultOuterTpl;
    } else {
        $tpl = $optionTpl;
        $outerTpl = $formOuterTpl;
    }
} else {
    if ($modx->getObject('xpAnswer', array('uid' => $uid, 'qid' => $id)) || $abstain == true || $hideForm == true) {
        $tpl = $resultTpl;
        $outerTpl = $resultOuterTpl;
    } else {
        $tpl = $optionTpl;
        $outerTpl = $formOuterTpl;
    }
}

$q = $modx->newQuery('xpOption');
$q->where(array('qid' => $id));
$q->select('`xpOption`.`id`, `xpOption`.`qid`, `xpOption`.`option`, `xpOption`.`rank`,
            `xpOption`.`right`, `xpQuestion`.`text`, `xpQuestion`.`type`, COUNT(`xpAnswer`.`uid`) as `votes`');
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
    if (empty($output['type'])) $output['type'] = $options[0]['type'];
    if (empty($output['id'])) $output['id'] = $options[0]['qid'];
    // print_r($options);die;
    foreach ($options as $option) {
        if($output['maxVotes'] != 0) {
            $option['percentVotes'] = round($option['votes'] / $output['maxVotes'] * 100, 2);
        } else {
            $option['percentVotes'] = 0;
        }
        $option['option'] = $modx->lexicon("option_" . $option['qid'] ."_". $option['id']);
        //print_r($option);
        $output['options'][] = $modx->getChunk($tpl,$option);
        // print_r($output);die;
    }
    // print $tpl; die;
    // print_r($output['options']); die;
    $output['options'] = implode($outputSeparator, $output['options']);
    $output = $xPoller2->getChunk($outerTpl, $output);
} else {
    $output = $modx->lexicon("xpoller2_question_err_ns");
}
print $output;