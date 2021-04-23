<?php
/**
 * @var $object
 * @var $key string
 */
$filterKey = $key;

if(empty($object)){
    $object = "<span style=\"color: gray\">N/A</span>";
}elseif(is_array($object)){
    $tmp = $object; $objArray = array();
    foreach ($tmp as $key => $item){
        $route = route('lily.index',['filterBy' => $filterKey, 'filterValue' => $item]);
        $objArray[$key] = "<a href=\"$route\">".e($item)."</a>";
    }
    $object = implode(', ', $objArray);
}else{
    $route = route('lily.index',['filterBy' => $filterKey, 'filterValue' => $object]);
    $object = "<a href=\"$route\">".e($object)."</a>";
}

?>
@if(mb_strlen(strip_tags($object) ?? '') > 27)
    <tr>
        <th>{{ $th }}</th>
        <td rowspan="2" style="height: 4em;">{!! $object !!}</td>
    </tr>
    <tr>
        <td class="spacer"></td>
    </tr>
@else
    <tr>
        <th>{{ $th }}</th>
        <td>{!! $object !!}</td>
    </tr>
@endif

