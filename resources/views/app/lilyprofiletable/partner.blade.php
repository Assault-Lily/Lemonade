@if($partner instanceof \App\Models\Lily)
    <?php
        /**
         * @var $partner \App\Models\Lily
         */
    ?>
    <tr>
        <th>{{ $th }}</th>
        <td><a href="{{ route('lily.show',['lily' => $partner->slug]) }}">
                {{ $partner->name }}</a></td>
    </tr>
@else
    <tr>
        <th>{{ $th }}</th>
        <td>{{ $partner }}</td>
    </tr>
@endif
