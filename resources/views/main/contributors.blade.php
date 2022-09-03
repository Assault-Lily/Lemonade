@extends('app.layout', ['title' => 'Contributors'])

@section('head')
    <style>
        #twin {
            display: flex;
            width: 100%;
            box-sizing: border-box;
            gap: 40px;
        }
        #twin > div {
            width: 100%;
        }
        .list-item-a {
            width: calc(50% - 12px);
        }
        #illustration .list-item-b {
            display: inline-block;
            width: calc(25% - 11px);
            margin-right: 10px;
            padding: 8px 18px;
        }
        #illustration .list-item-b:nth-child(4n) {
            margin-right: 0;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>コントリビューター一覧</h1>
        <p class="center">
            LemonadeとLuciaDBはご覧の皆様のご協力・ご尽力により開発・運用されています。
        </p>
        <div id="twin">
            <div id="lemonade">
                <h2>Lemonade</h2>
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between">
                    <a class="list-item-a" href="https://dev.lemonade.lily.garden/lily/Aizawa_Kazuha">
                        <div class="list-item-image">
                            <img src="https://github.com/miyacorata.png" alt="icon">
                        </div>
                        <div class="list-item-data">
                            <div class="title-ruby">miyacorata</div>
                            <div class="title">K Miyano</div>
                            <div>Contributions : N</div>
                        </div>
                    </a>
                    <a class="list-item-a" href="https://dev.lemonade.lily.garden/lily/Aizawa_Kazuha">
                        <div class="list-item-image">
                            <img src="https://github.com/dependabot.png" alt="icon">
                        </div>
                        <div class="list-item-data">
                            <div class="title-ruby">contributor</div>
                            <div class="title">コントリビュータ</div>
                            <div>Contributions : N</div>
                        </div>
                    </a>
                </div>
            </div>
            <div id="lucia-db">
                <h2>LuciaDB</h2>
                <div style="display: flex; flex-wrap: wrap; ">
                    <a class="list-item-a" href="https://dev.lemonade.lily.garden/lily/Aizawa_Kazuha">
                        <div class="list-item-image">
                            <img src="https://github.com/fvh-P.png" alt="icon">
                        </div>
                        <div class="list-item-data">
                            <div class="title-ruby">fvh-P</div>
                            <div class="title">ふぁぼ原</div>
                            <div>Contributions : N</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div>
            <h2>イラスト寄稿</h2>
            <p class="center">
                Lemonadeでは以下の皆様からご提供いただいたイラストをアイコンとして使用させていただいています。
            </p>
            <div id="illustration">
                <div class="list-item-b">
                    <div class="list-item-data">
                        <div class="title">K Miyano</div>
                    </div>
                </div>
                <div class="list-item-b">
                    <div class="list-item-data">
                        <div class="title">Name</div>
                    </div>
                </div>
                <div class="list-item-b">
                    <div class="list-item-data">
                        <div class="title">名前</div>
                    </div>
                </div>
                <div class="list-item-b">
                    <div class="list-item-data">
                        <div class="title">コントリビュータ</div>
                    </div>
                </div>
                <div class="list-item-b">
                    <div class="list-item-data">
                        <div class="title">コントリビュータ</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
