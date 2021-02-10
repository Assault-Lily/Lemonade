@if(mb_strlen($object ?? '') > 24)
    @include('app.lilyprofiletable.record2line',['object' => $object, 'th' => $th])
@else
    <tr>
        <th>{{ $th }}</th>
        <td>{{ $object ?? 'N/A' }}</td>
    </tr>
@endif

