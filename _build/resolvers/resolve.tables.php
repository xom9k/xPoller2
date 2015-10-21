<?php

if ($object->xpdo) {
	/* @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
			$modelPath = $modx->getOption('xpoller2_core_path',null,$modx->getOption('core_path').'components/xpoller2/').'model/';
			$modx->addPackage('xpoller2', $modelPath);

			$manager = $modx->getManager();
			$objects = array(
				'xpTest',
				'xpQuestion',
				'xpOption',
				'xpAnswer'
			);
			foreach ($objects as $tmp) {
				$manager->createObjectContainer($tmp);
			}
			break;

		case xPDOTransport::ACTION_UPGRADE:
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;
