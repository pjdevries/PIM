<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

extract($displayData);

// Get some system objects.
$document = Factory::getApplication()->getDocument();

/**
 * Layout variables
 * -----------------
 * @var   string $autocomplete Autocomplete attribute for the field.
 * @var   boolean $autofocus Is autofocus enabled?
 * @var   string $class Classes for the input.
 * @var   string $description Description of the field.
 * @var   boolean $disabled Is this field disabled?
 * @var   string $group Group the field belongs to. <fields> section in form XML.
 * @var   boolean $hidden Is this field hidden in the form?
 * @var   string $hint Placeholder for the field.
 * @var   string $id DOM id of the field.
 * @var   string $label Label of the field.
 * @var   string $labelclass Classes to apply to the label.
 * @var   boolean $multiple Does this field support multiple values?
 * @var   string $name Name of the input field.
 * @var   string $onchange Onchange attribute for the field.
 * @var   string $onclick Onclick attribute for the field.
 * @var   string $pattern Pattern (Reg Ex) of value of the form field.
 * @var   boolean $readonly Is this field read only?
 * @var   boolean $repeat Allows extensions to duplicate elements.
 * @var   boolean $required Is this field required?
 * @var   integer $size Size attribute of the input.
 * @var   boolean $spellcheck Spellcheck state for the form field.
 * @var   string $validate Validation rules to apply.
 * @var   string $value Value attribute of the field.
 * @var   array $checkedOptions Options that will be set as checked.
 * @var   boolean $hasValue Has this field a value assigned?
 * @var   array $options Options available for this field.
 *
 * Field Specific
 */

$inputvalue = '';

// Build the attributes array.
$attributes = array();

empty($size) ? null : $attributes['size'] = $size;
empty($maxlength) ? null : $attributes['maxlength'] = ' maxlength="' . $maxLength . '"';
empty($class) ? null : $attributes['class'] = $class;
!$readonly ? null : $attributes['readonly'] = 'readonly';
!$disabled ? null : $attributes['disabled'] = 'disabled';
empty($onchange) ? null : $attributes['onchange'] = $onchange;

if ($required) {
    $attributes['required'] = '';
    $attributes['aria-required'] = 'true';
}

// Handle the special case for "now".
if (strtoupper($value) == 'NOW') {
    $value = Factory::getDate()->format('Y-m-d H:i:s');
}

$readonly = isset($attributes['readonly']) && $attributes['readonly'] == 'readonly';
$disabled = isset($attributes['disabled']) && $attributes['disabled'] == 'disabled';

if (is_array($attributes)) {
    $attributes = ArrayHelper::toString($attributes);
}

$cssFileExt = (($direction ?? '') === 'rtl') ? '-rtl.css' : '.css';

$inputValue = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
$inputHint = !empty($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '';
$inputAlt = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');

$buttonId = $id . '_btn';
$buttonVisibility = ($readonly || $disabled) ? 'hidden ' : '';
?>
<div class="field-calendar">
    <?php
    if (!$readonly && !$disabled) : ?>
    <div class="input-append">
        <?php
        endif; ?>

        <input type="text" id="<?= $id; ?>" name="<?=  $name; ?>" value="<?= $inputValue ?>" <?= $attributes; ?>
            <?= $inputHint ?> data-alt-value="<?= $inputAlt ?>" autocomplete="off" style="background-color: #eee;"/>

        <button type="button" class="<?= $buttonVisibility ?> btn btn-secondary"
                id="<?= $buttonId; ?>"
                title="<?= Text::_('PLG_SYSTEM_PIM_API_REFRESH_KEY_BUTTON_LABEL'); ?>"
        >
            <span class="icon-refresh" aria-hidden="true"></span>
        </button>
        <?php
        if (!$readonly && !$disabled) : ?>
    </div>
<?php
endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', e => {
            const elInput = document.getElementById('<?= $id; ?>');
            const elButton = document.getElementById('<?= $buttonId; ?>');

            if (!(elInput && elButton)) {
                return;
            }

            if (elInput.value.trim() === '') {
                elInput.value = crypto.randomUUID();
            }

            elButton.addEventListener('click', e => elInput.value = crypto.randomUUID());
        });
    </script>
</div>
