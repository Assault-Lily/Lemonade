<?php
/** @var $object */
if(is_array($object)){
    $array = $object;
    $object = '';
    foreach ($array as $value){
        $object .= $value.', ';
    }
    $object = mb_substr($object, 0, mb_strlen($object) - 2);
}
if(!empty($suffix)){
    $object .= $suffix;
}
if(!empty($prefix)){
    $object = $prefix.$object;
}
?>
@if(mb_strlen($object ?? '') > 27 || (!empty($multiline) && $multiline))
    <tr>
        <th>{{ $th }}</th>
        <td rowspan="2" style="height: 4em;">{!! empty($object) ? "<span style=\"color:gray;\">N/A</span>" : e($object) !!}</td>
    </tr>
    <tr>
        <td class="spacer"></td>
    </tr>
@else
    <tr>
        <th>{{ $th }}</th>
        <td>{!! empty($object) ? "<span style=\"color:gray;\">N/A</span>" : e($object) !!}</td>
    </tr>
@endif

