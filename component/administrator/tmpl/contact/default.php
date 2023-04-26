<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.formvalidator');

$this->document->getWebAssetManager()->addScriptDeclaration("
	Joomla.submitbutton = function (task) {
		if (task == 'contact.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
");
?>

<form action="<?php echo Route::_('index.php?option=com_volunteers&view=contact'); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">
    <?php if ($this->recipients) : ?>
    <h3><?php echo count($this->recipients); ?> recipients</h3>
    <table class="table table-striped">
        <?php foreach ($this->recipients as $recipient) : ?>
            <tr>
                <td width="200px"><strong><?php echo $recipient['name']; ?></strong></td>
                <td width="300px"> <?php echo $recipient['email']; ?></td>
                <td width="200px"> <?php echo $recipient['position']; ?></td>
                <td> <?php echo $recipient['team']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </table>
    <h3>Email</h3>
    <?php echo $this->form->renderFieldset('message'); ?>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
