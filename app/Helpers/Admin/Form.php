<?php


namespace App\Helpers\Admin;


class Form
{
    /*
     * Возвращает input для формы.
     * $name - передать название, перевод будет взять из /resources/lang/en/s.php.
     * $value - передать значение, необязательный параметр.
     * $required - если input необязательный, то передайте null, необязательный параметр.
     * $type - тип input, по-умолчанию text, необязательный параметр.
     * $label - если он нужен, то передать true, необязательный параметр.
     * $placeholder - если нужен другой текст, то передать его, необязательный параметр.
     * $class - передайте свой класс, необязательный параметр.
     * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest', 'novalidate' => ''], необязательный параметр.
     * $classInput - передайте свой класс для input, необязательный параметр.
     * $id - Передайте свой id, необязательный параметр.
     * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
     * $appendAfterInput - вставить код после input, например иконку input-group-append.
     */
    public static function input($name, $value = null, $required = true, $type = null, $label = true, $placeholder = null, $class = null, $attrs = [], $classInput = null, $id = null, $idForm = null, $appendAfterInput = null)
    {
        $title = l($name, 'a');
        $id = $idForm ? "{$idForm}_{$id}" : $id;
        $id = $id ?: $name;

        $required = $required ? 'required' : null;
        $type = $type ? $type : 'text';
        $star = $required ? '<sup>*</sup>' : null;

        // Проверим значение на 0
        if ($value === '0' || $value === 0) {
            $value = '0';
        } elseif (empty($value)) {
            $value = old($name);
        }
        //$value = $value ?: old($name);

        $placeholderStar = !$label && $required ? '*' : null;
        $placeholderLabel = !$label && !$required || $label ? '...' : null;
        $placeholder = ($placeholder ?: $title) . $placeholderStar . $placeholderLabel;
        $label = $label ? null : 'class="sr-only"';
        $label = "<label for='{$id}' {$label}>$title $star</label>";
        $labelBefore = $appendAfterInput ? $label : null;
        $labelAfter = $appendAfterInput ? null : $label;

        $inputGroup = $appendAfterInput ? 'input-group' : 'form-group';
        $part = '';

        if ($attrs) {
            foreach ($attrs as $k => $v) {
                if ($v) {
                    $part .= "{$k}='{$v}' ";
                } else {
                    $part .= "$k ";
                }

            }
        }

        return <<<S
$labelBefore
<div class="{$inputGroup} {$class}">
    $labelAfter
    <input type="{$type}" name="{$name}" id="{$id}" class="form-control {$classInput}" aria-describedby="{$name}" placeholder="{$placeholder}" value="{$value}" $part {$required}>
    $appendAfterInput
</div>
S;
    }


    /*
     * Возвращает textarea для формы.
     * $name - передать название, перевод будет взять из /resources/lang/en/s.php.
     * $value - передать значение, необязательный параметр.
     * $required - если input необязательный, то передайте null, необязательный параметр.
     * $label - если он нужен, то передать true, необязательный параметр.
     * $placeholder - если нужен другой текст, то передать его, необязательный параметр.
     * $class - передайте свой класс, необязательный параметр.
     * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest', 'novalidate' => ''], необязательный параметр.
     * $rows - кол-во рядов, по-умолчанию 3, необязательный параметр.
     * $id - Передайте свой id, необязательный параметр.
     * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
     * $htmlspecialchars - $value обёртываем в функцию htmlspecialchars, передайте false, если не надо.
     */
    public static function textarea($name, $value = null, $required = true, $label = true, $placeholder = null, $class = null, $attrs = [], $rows = 3, $id = null, $idForm = null, $htmlspecialchars = true)
    {
        $title = l($name, 'a');
        $id = $idForm ? "{$idForm}_{$id}" : $id;
        $id = $id ?: $name;

        $required = $required ? 'required' : null;
        $star = $required ? '<sup>*</sup>' : null;
        $value = $value ?: old($name);
        $value = $htmlspecialchars ? e($value) : $value;

        $placeholderStar = !$label && $required ? '*' : null;
        $placeholderLabel = !$label && !$required || $label ? '...' : null;
        $placeholder = ($placeholder ?: $title) . $placeholderStar . $placeholderLabel;

        $label = $label ? null : 'class="sr-only"';
        $rows = (int)$rows;
        $part = '';
        if ($attrs) {
            foreach ($attrs as $k => $v) {
                if ($v) {
                    $part .= "{$k}='{$v}' ";
                } else {
                    $part .= "$k ";
                }
            }
        }

        return <<<S
<div class="form-group">
    <label for="{$id}" {$label}>$title $star</label>
    <textarea name="{$name}" id="{$id}" class="form-control {$class}" placeholder="{$placeholder}" rows="{$rows}" $part {$required}>{$value}</textarea>
</div>
S;
    }


    /*
     * Возвращает select для формы.
     * $name - передать название, перевод будет взять из /resources/lang/en/s.php.
     * $options - передать в массиве options (если $value будет равна одму из значений этого массива, то этот option будет selected).
     * $value - передать значение, необязательный параметр.
     * $label - если он нужен, то передать true, необязательный параметр.
     * $class - передайте свой класс, необязательный параметр.
     * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest'], необязательный параметр.
     * $option_id_value - передайте true, если передаёте массив $options, в котором ключи это id для вывода как значения для option, необязательный параметр.
     * $disabledValue - передать значения, для которого установить атрибут disabled.
     * $id - Передайте свой id, необязательный параметр.
     * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
     */
    public static function select($name, $options, $value = null, $label = true, $class = null, $attrs = null, $option_id_value = null, $disabledValue = null, $id = null, $idForm = null)
    {
        $title = l($name, 'a');
        $id = $idForm ? "{$idForm}_{$id}" : $id;
        $id = $id ?: $name;
        $value = $value ?: old($name) ?: null;
        $label = $label ? null : 'class="sr-only"';

        // Принимает в объекте 2 параметра, первый - value для option, второй название для option
        $opts = '';
        if ($options) {
            foreach ($options as $k => $v) {
                $t = l($v, 'a');
                $v = $option_id_value ? $k : $v;
                $selected = $value === $v ? ' selected' : null;
                $disabled = $disabledValue && $k == $disabledValue ? ' disabled' : null;
                $opts .= "<option value='{$v}' {$selected}{$disabled}>{$t}</option>\n";

            }
        }

        $part = '';
        if ($attrs && is_array($attrs)) {
            foreach ($attrs as $k => $v) {
                $part .= "{$k}='{$v}' ";
            }
        } else {
            $part = $attrs;
        }

        return <<<S
<div class="form-group $class">
    <label for="{$id}" {$label}>{$title}</label>
    <select class="form-control" name="{$name}" id="{$id}" {$part}>
        $opts
    </select>
</div>
S;
    }


    /*
     * Иконка для input.
     * $icon - классы иконок fontawesome.
     * $classMain - класс для основного блока, необязательный параметр.
     * $classText - класс для вложенного блока, необязательный параметр.
     * $classIcon - класс для иконки, необязательный параметр.
     * $attrs - передайте необходимые параметры строкой или в массиве ['id' => 'test', 'data-id' => 'dataTest'], необязательный параметр.
     */
    public static function inputGroupAppend($icon, $classMain = null, $classText = null, $classIcon = null, $attrs = null)
    {
        $part = '';
        if ($attrs && is_array($attrs)) {
            foreach ($attrs as $k => $v) {
                $part .= "{$k}='{$v}' ";
            }
        } else {
            $part = $attrs;
        }
        return <<<S
<div class="input-group-append {$classMain}" {$part}>
    <span class="input-group-text {$classText}">
        <i class="{$icon} {$classIcon}"></i>
    </span>
</div>
S;
    }


    /*
     * Возвращает скрытый input для формы.
     * $name - Передать имя input.
     * $value - Значение.
     * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest'], необязательный параметр.
     */
    public static function hidden($name, $value, $attrs = null)
    {
        if ($attrs) {
            foreach ($attrs as $k => $v) {
                if ($v) {
                    $part .= "{$k}='{$v}' ";
                } else {
                    $part .= "$k ";
                }

            }
        }
        return "<input type=\"hidden\" name=\"{$name}\" value='{$value}' {$attrs}>";
    }
}
