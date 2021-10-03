<?php
    /**
     * @var $lilies array
     * @var $legions array
     * @var $lily array
     * @var $icons array
     */
    $ogp['description'] = "リリィの一覧を表示します。現在".count($lilies)."のリリィが登録されています。";
    $ogp['title'] = 'リリィ一覧';
    $typeInfo = explode(',', request()->get('type','lily'));
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
                const lily = document.getElementById('random-lily');
                if(lily.firstChild) lily.removeChild(lily.firstChild);
                const lilies = document.querySelectorAll('#lily-list > .list-item-a:not(.hidden)');
                if(lilies.length === 0){
                    showMessage("該当するデータがないためランダム選択できません。");
                }else{
                    let filteredLilies = Array.from(lilies).filter((lily) => lily.classList.length === 1);
                    lily.appendChild(filteredLilies[Math.floor(Math.random() * filteredLilies.length)].cloneNode(true));
                    randomDialog.showModal();
                }
            });
            document.getElementById('random-close').addEventListener('click',()=>{
                randomDialog.close();
            });

            const nameFilter = document.getElementById('name-filter');
            const emptyNotice = document.createElement('p');
            emptyNotice.id = 'emptyNotice';
            emptyNotice.classList.add('center', 'notice');
            emptyNotice.style = 'margin:3em auto;';
            emptyNotice.innerText = '該当するデータがありません';
            nameFilter.addEventListener('input', ()=>{
                const lilies = document.querySelectorAll('#lily-list > .list-item-a');
                if(lilies.length === 0) return;
                let search = nameFilter.value;
                if((new RegExp(/[ａ-ｚＡ-Ｚ]/)).test(search[search.length - 1])){
                    // 最後の文字が全角英数であれば、その文字は考慮しない
                    search = search.substr(0, search.length - 1);
                }
                let count = 0;
                lilies.forEach((lily)=>{
                    if(search.length === 0 ||
                        lily.querySelector('.title').innerText.indexOf(search) !== -1 ||
                        lily.querySelector('.title-ruby').innerText.indexOf(search) !== -1){
                        lily.classList.remove('hidden');
                        count++;
                    }else{
                        lily.classList.add('hidden');
                    }
                });
                if(count === 0){
                    if(!document.getElementById('emptyNotice'))
                        document.getElementById('lily-list').parentNode.appendChild(emptyNotice);
                }else if(document.getElementById('emptyNotice')){
                    document.getElementById('emptyNotice').remove();
                }
            });

            document.querySelectorAll('form[id$="-selector"]').forEach((el)=>{
                el.addEventListener('submit', (e)=>{
                    e.preventDefault();
                    let filterValue = el.querySelector('*[name=filterValue]').value;
                    let filterBy = el.querySelector('*[name=filterBy]').value;
                    location.href = changeGetParam('filterValue', filterValue, changeGetParam('filterBy', filterBy));
                });
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
        .list-item-a.hidden{
            display: none;
        }
    </style>
@endsection

@section('main')
    <main>

        <div class="top-options">
            <div>
                <label>
                    <select id="sort-select">
                        <option value="a-name">フルネーム | 昇順</option>
                        <option value="d-name">フルネーム | 降順</option>
                        <option value="a-givenName">名前 | 昇順</option>
                        <option value="d-givenName">名前 | 降順</option>
                        <option value="a-age">年齢 | 昇順</option>
                        <option value="d-age">年齢 | 降順</option>
                        <option value="a-birthdate">誕生日 | 昇順</option>
                        <option value="d-birthdate">誕生日 | 降順</option>
                        <option value="a-height">身長 | 昇順</option>
                        <option value="d-height">身長 | 降順</option>
                        <option value="a-weight">体重 | 昇順</option>
                        <option value="d-weight">体重 | 降順</option>
                        <option value="a-bloodType">血液型 | 昇順</option>
                        <option value="d-bloodType">血液型 | 降順</option>
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
                <button class="button smaller" id="filter-setting-open">表示・フィルタ設定</button>
                <button class="button smaller" id="random-open">ランダム</button>
            </div>
            <div>
                <label><input type="search" id="name-filter" placeholder="絞り込む" style="margin-right: 5px; width: 200px"></label>
                <span class="info">登録数{{ (!empty($filterInfo) || $typeInfo !== array('lily')) ? '(フィルタ)' : '(全数)' }} : {{ count($lilies) }}人</span>
            </div>
        </div>
        @if(!empty($filterInfo))
            <p class="center">
                @if(!empty($filterInfo['SP']))
                    <strong>{{ implode(' , ', $filterInfo['SP']) }}</strong>
                @endif
                @if(!empty($filterInfo['SP']) && !empty($filterInfo['key']) && !empty($filterInfo['value'])) かつ @endif
                @if(!empty($filterInfo['key']) && !empty($filterInfo['value']))
                    <strong>{{ $filterInfo['key'] }}</strong> が
                    <strong>"{{ $filterInfo['value'].($filterInfo['suffix'] ?? '') }}"</strong>である
                @endif
                リリィでフィルタしています
                <a href="{{ route('lily.index') }}" class="button smaller">フィルタ解除</a>
            </p>
        @endif
        @if($typeInfo !== array('lily'))
            <?php
            $typeInfoJa = array_map(function ($value){
                switch ($value){
                    case 'lily':
                        return 'リリィ';
                    case 'character':
                        return 'その他';
                    case 'teacher':
                        return '教導官';
                    default:
                        return $value;
                }
            }, $typeInfo);
            ?>
            <p class="center">
                表示設定 : <strong>{{ implode(' , ', $typeInfoJa) }}</strong> の一覧を表示しています。
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
        </div>
    </main>

    <dialog id="filter-setting" class="window-a">
        <div class="header">表示・フィルタ設定</div>
        <div class="body">
            <div style="max-height: 450px; overflow-y: scroll; margin: 0 -20px; padding: 0 20px 5px">
                <h3>表示設定</h3>
                <div id="viewMode-selector">
                    <a href="javascript:location.href = changeGetParam('type', null)" class="button">リリィのみ</a>
                    <a href="javascript:location.href = changeGetParam('type', 'lily,teacher')" class="button">教導官を含む</a>
                    <a href="javascript:location.href = changeGetParam('type', 'teacher')" class="button">教導官のみ</a>
                    <a href="javascript:location.href = changeGetParam('type', 'lily,character')" class="button">その他を含む</a>
                    <a href="javascript:location.href = changeGetParam('type', 'character')" class="button">その他のみ</a>
                    <a href="javascript:location.href = changeGetParam('type', 'lily,teacher,character')" class="button">すべて</a>
                </div>

                <h3>生命状態</h3>
                <div id="boosted-selector">
                    <a href="javascript:location.href = changeGetParam('lifeStatus', null)" class="button">考慮しない</a>
                    <a href="javascript:location.href = changeGetParam('lifeStatus', 'alive')" class="button">存命</a>
                    <a href="javascript:location.href = changeGetParam('lifeStatus', 'dead')" class="button">殉職・故人</a>
                    <a href="javascript:location.href = changeGetParam('lifeStatus', 'unknown')" class="button">生死不明</a>
                </div>
                <h3>強化リリィ</h3>
                <div id="boosted-selector">
                    <a href="javascript:location.href = changeGetParam('isBoosted', null)" class="button">考慮しない</a>
                    <a href="javascript:location.href = changeGetParam('isBoosted', 'true')" class="button">強化リリィ</a>
                    <a href="javascript:location.href = changeGetParam('isBoosted', 'false')" class="button">強化済でない</a>
                </div>

                <hr>

                <h3>ガーデン</h3>
                <form action="{{ route('lily.index') }}" method="get" id="garden-selector" name="garden">
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
                    <a href="javascript:location.href = changeGetParam('filterValue', 'AZ', changeGetParam('filterBy', 'position'))" class="button">AZ</a>
                    <a href="javascript:location.href = changeGetParam('filterValue', 'TZ', changeGetParam('filterBy', 'position'))" class="button">TZ</a>
                    <a href="javascript:location.href = changeGetParam('filterValue', 'BZ', changeGetParam('filterBy', 'position'))" class="button">BZ</a>
                </div>
                <h3>血液型</h3>
                <div id="bloodType-selector">
                    <a href="javascript:location.href = changeGetParam('filterValue', 'A',  changeGetParam('filterBy', 'bloodType'))" class="button">A</a>
                    <a href="javascript:location.href = changeGetParam('filterValue', 'B',  changeGetParam('filterBy', 'bloodType'))" class="button">B</a>
                    <a href="javascript:location.href = changeGetParam('filterValue', 'O',  changeGetParam('filterBy', 'bloodType'))" class="button">O</a>
                    <a href="javascript:location.href = changeGetParam('filterValue', 'AB', changeGetParam('filterBy', 'bloodType'))" class="button">AB</a>
                </div>
                <h3>スキル</h3>
                <form action="{{ route('lily.index') }}" method="get" id="skill-selector" name="skill">
                    <label>
                        <select name="filterBy" id="skill-type">
                            <option value="skill" selected>スキル(全種)</option>
                            <option value="rareSkill">レアスキル</option>
                            <option value="subSkill">サブスキル</option>
                            <option value="boostedSkill">ブーステッドスキル</option>
                        </select>
                    </label>
                    <label>
                        <input type="text" name="filterValue" placeholder="スキル名" list="skill" id="skillName" required>
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
                    <datalist id="skill">
                        @foreach($datalist['skill'] as $skill)
                            <option value="{{ $skill }}"></option>
                        @endforeach
                    </datalist>
                    <input type="submit" value="フィルタ" class="button primary">
                </form>
            </div>

            <hr>
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
                    @if(!empty($filterInfo['SP']))
                        {{ implode(', ', $filterInfo['SP']) }}
                    @endif
                    @if(!empty($filterInfo['key']) && !empty($filterInfo['value']))
                        <strong>{{ $filterInfo['key'] }}</strong> が
                        <strong>"{{ $filterInfo['value'].($filterInfo['suffix'] ?? '') }}"</strong>
                    @endif
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
