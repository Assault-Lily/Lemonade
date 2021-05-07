<?php
    /**
     * @var $lilies array
     * @var $legions array
     * @var $lily array
     * @var $icons array
     */
    $ogp['description'] = "リリィの一覧を表示します。現在".count($lilies)."のリリィが登録されています。";
    $ogp['title'] = 'リリィ一覧';
?>
@extends('app.layout', ['page-type' => 'back-triangle', 'title' => 'リリィ一覧', 'ogp' => $ogp])

@section('head')
    <script type="application/javascript">
        document.addEventListener('DOMContentLoaded',()=>{
            document.querySelectorAll('#sort-select > option').forEach((item)=>{
                if(item.attributes.value.value === "{{ $sortKey }}")
                    item.setAttribute('selected', true);
            });
            document.getElementById('sort-select').addEventListener('change',(e)=>{
                let order = e.target.value.substr(0,1);
                switch (order){
                    case 'a':
                        order = 'asc'; break;
                    case 'd':
                        order = 'desc'; break;
                    default:
                        order = false;
                }
                let sort = e.target.value.substr(2);
                let url = new URL(location.href);
                url.searchParams.set('sort',sort);
                if(order !== false) url.searchParams.set('order',order);
                location.href = url.toString();
                document.body.classList.add('hide');
            });
            const filterDialog = document.getElementById('filter-setting');
            dialogPolyfill.registerDialog(filterDialog);
            filterDialog.querySelectorAll('a.button').forEach((el)=>{
                el.addEventListener('click', ()=>{
                    filterDialog.close();
                });
            });
            filterDialog.querySelectorAll('form').forEach((el)=>{
                el.addEventListener('submit', ()=>{
                    filterDialog.close();
                    document.body.classList.add('hide');
                });
            });
            document.getElementById('filter-setting-open').addEventListener('click',()=>{
                filterDialog.showModal();
            });
            document.getElementById('filter-setting-close').addEventListener('click',()=>{
                filterDialog.close();
            });
            document.getElementById('skill-type').addEventListener('change',(e)=>{
                document.getElementById('skillName').setAttribute('list', e.target.value);
            });

            const randomDialog = document.getElementById('random');
            dialogPolyfill.registerDialog(randomDialog);
            document.getElementById('random-open').addEventListener('click',()=>{
                const lilies = document.querySelectorAll('#lily-list > .list-item-a');
                const lily = document.getElementById('random-lily');
                if(lily.firstChild) lily.removeChild(lily.firstChild);
                if(lilies.length === 0){
                    showMessage("該当するリリィがいないためランダム選択できません。");
                }else{
                    lily.appendChild(lilies[Math.floor(Math.random() * lilies.length)].cloneNode(true));
                    randomDialog.showModal();
                }
            });
            document.getElementById('random-close').addEventListener('click',()=>{
                randomDialog.close();
            });
        });
    </script>
    <style>
        @media screen and (max-width: 500px) and (orientation: portrait) {
            #position-selector{
                text-align: center;
            }
            #position-selector > .button {
                min-width: calc((100% / 3) - 25px);
            }
            #filter-setting form{
                text-align: center;
            }
            #filter-setting input{
                margin: 5px;
            }
        }

        #random-lily{
            display: flex;
            justify-content: space-around;
        }
        #random-lily > .list-item-a{
            width: 420px;
        }
    </style>
@endsection

@section('main')
    <main>

        <div class="top-options">
            <div>
                <label>
                    ならべかえ :
                    <select id="sort-select">
                        <option value="a-name">フルネーム | 昇順</option>
                        <option value="d-name">フルネーム | 降順</option>
                        <option value="a-givenName">名前 | 昇順</option>
                        <option value="d-givenName">名前 | 降順</option>
                        <option value="a-age">年齢 | 昇順</option>
                        <option value="d-age">年齢 | 降順</option>
                        <option value="a-position">ポジション | 昇順</option>
                        <option value="d-position">ポジション | 降順</option>
                        <option value="a-rareSkill">レアスキル | 昇順</option>
                        <option value="d-rareSkill">レアスキル | 降順</option>
                        <option value="a-legion">レギオン | 昇順</option>
                        <option value="d-legion">レギオン | 降順</option>
                        <option value="a-garden">ガーデン | 昇順</option>
                        <option value="d-garden">ガーデン | 降順</option>
                    </select>
                </label>
                <button class="button smaller" id="filter-setting-open">フィルタ設定</button>
                <button class="button smaller" id="random-open">ランダム</button>
            </div>
            <div>
                <span class="info">リリィ登録数{{ !empty($filterInfo) ? '(フィルタ)' : '(全数)' }} : {{ count($lilies) }}人</span>
            </div>
        </div>
        @if(!empty($filterInfo))
            <p class="center">
                <strong>{{ $filterInfo['key'] }}</strong> が
                <strong>"{{ $filterInfo['value'] }}"</strong>
                のリリィでフィルタしています
                <a href="{{ route('lily.index') }}" class="button smaller">フィルタ解除</a>
            </p>
        @endif
        <div class="list three" id="lily-list">
            @forelse($lilies as $key => $lily)
                <?php /** @var $key string */
                $legion = $legions[$lily['lily:legion'][0] ?? ''] ?? array();
                $icon = $icons[$key] ?? array();
                ?>
                @include('app.button_lily',['key' => $key, 'lily' => $lily, 'legion' => $legion, 'additional' => $additional, 'icons' => $icon])
            @empty
                <p style="text-align: center; color: darkred; margin: 3em auto">該当するデータがありません</p>
            @endforelse
            @if((count($lilies) % 3) != 0)
                <div style="width: 32%; margin-left: 6px"></div>
            @endif
        </div>
    </main>

    <dialog id="filter-setting" class="window-a">
        <div class="header">フィルタ設定</div>
        <div class="body">
            <p class="center">
                フィルタ設定を変更するとソートは一旦解除されます。
            </p>
            <h3>ガーデン</h3>
            <form action="{{ route('lily.index') }}" method="get">
                <input type="hidden" name="filterBy" value="garden">
                <label>
                    <input type="text" name="filterValue" placeholder="ガーデン名" list="gardenList" required>
                </label>
                <datalist id="gardenList">
                    @foreach($datalist['garden'] as $garden)
                        <option value="{{ $garden }}"></option>
                    @endforeach
                </datalist>
                <input type="submit" value="フィルタ" class="button primary">
            </form>
            <h3>ポジション</h3>
            <div id="position-selector">
                <a href="{{ route('lily.index', ['filterBy' => 'position', 'filterValue' => 'AZ']) }}" class="button">AZ</a>
                <a href="{{ route('lily.index', ['filterBy' => 'position', 'filterValue' => 'TZ']) }}" class="button">TZ</a>
                <a href="{{ route('lily.index', ['filterBy' => 'position', 'filterValue' => 'BZ']) }}" class="button">BZ</a>
            </div>
            <h3>スキル</h3>
            <form action="{{ route('lily.index') }}" method="get">
                <label>
                    <select name="filterBy" id="skill-type">
                        <option value="rareSkill" selected>レアスキル</option>
                        <option value="subSkill">サブスキル</option>
                        <option value="boostedSkill">ブーステッドスキル</option>
                    </select>
                </label>
                <label>
                    <input type="text" name="filterValue" placeholder="スキル名" list="rareSkill" id="skillName" required>
                </label>
                <datalist id="rareSkill">
                    @foreach($datalist['rareSkill'] as $rareSkill)
                        <option value="{{ $rareSkill }}"></option>
                    @endforeach
                </datalist>
                <datalist id="subSkill">
                    @foreach($datalist['subSkill'] as $subSkill)
                        <option value="{{ $subSkill }}"></option>
                    @endforeach
                </datalist>
                <datalist id="boostedSkill">
                    @foreach($datalist['boostedSkill'] as $boostedSkill)
                        <option value="{{ $boostedSkill }}"></option>
                    @endforeach
                </datalist>
                <input type="submit" value="フィルタ" class="button primary">
            </form>
            <div class="buttons">
                <a href="{{ route('lily.index') }}" class="button">リセット</a>
                <button id="filter-setting-close" class="button">閉じる</button>
            </div>
        </div>
    </dialog>

    <dialog id="random" class="window-a">
        <div class="header">ランダム選択</div>
        <div class="body">
            @if(!empty($filterInfo))
                <p class="center">
                    <strong>{{ $filterInfo['key'] }}</strong> が
                    <strong>"{{ $filterInfo['value'] }}"</strong>
                    のリリィからランダムで選んでいます。
                </p>
            @else
                <p class="center">
                    すべてのリリィからランダムで選んでいます。
                </p>
            @endif
            <div id="random-lily"></div>
            <div class="buttons">
                <button id="random-close" class="button">閉じる</button>
            </div>
        </div>
    </dialog>
@endsection
