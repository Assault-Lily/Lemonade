@if(!empty($partner_data))
    <tr>
        <th>{{ $th }}</th>
        <td><a href="{{ route('lily.show',['lily' => str_replace('lilyrdf:', '', $partner)]) }}">
                {{ $partner_data['schema:name'][0] }}</a></td>
    </tr>
@else
    <tr>
        <th>{{ $th }}</th>
        <td>{{ $partner }}</td>
    </tr>
@endif
