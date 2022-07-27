@include('partials.form.checkbox', [
    'name' => $form->getField($field)->getRealName(),
    'value' => ($value = $form->getField($field)->getOption('value'))?$value:1,
    'id' => $form->getField($field)->getOption('id'),
    'label' => $form->getField($field)->getOption('label'),
])
@php
    $form->remove($field);
@endphp