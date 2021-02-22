@if(mb_strlen($object ?? '') > 24)
    @include('app.lilyprofiletable.record2line',['object' => $object, 'th' => $th])
@else
    <tr>
        <th>{{ $th }}</th>
        <td>{!! empty($object) ? "<span style=\"color:gray;\">N/A</span>" : e($object) !!}</td>
    </tr>
@endif

