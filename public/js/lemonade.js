// 読み込み中に画面を隠すやつ
document.querySelectorAll('a:not([target]):not(.sf-dump-toggle)').forEach((el)=>{
    el.addEventListener('click', (e)=>{
        if(!e.ctrlKey && !e.shiftKey) document.getElementsByTagName('body')[0].classList.add('hide');
    });
});

window.addEventListener("pageshow", function(event){
    if (event.persisted) {
        // ここにキャッシュ有効時の処理を書く

        const body = document.getElementsByTagName('body')[0];

        if (body.classList.contains('hide')) {
            body.classList.remove('hide');
            void body.offsetWidth;
        }

        if (body.classList.contains('fade-in')) {
            body.classList.remove('fade-in');
            void body.offsetWidth;
            body.classList.add('fade-in');
        }

        const header = document.getElementsByTagName('header')[0];
        if (header.classList.contains('float-in')) {
            header.classList.remove('float-in');
            void body.offsetWidth;
            header.classList.add('float-in');
        }
    }
});

document.getElementById('pageBackButton').addEventListener('click',(event)=>{
    event.preventDefault();
    let backed = false;
    window.onbeforeunload = function (){
        document.getElementsByTagName('body')[0].classList.add('hide');
        backed = true;
    };
    if(document.referrer.startsWith(location.protocol+'//'+location.hostname)){ // リファラが自ドメイン
        history.back(); // Void関数だけど試行
        if(!backed && window.confirm("戻り先がないようです。代わりにトップページに移動しますか？")){
            location.href = location.protocol+'//'+location.hostname;
        }
    }else{
        document.querySelectorAll('.flash-message').forEach((el) => {
            el.remove();
        });
        let notice = document.createElement('div');
        notice.classList.add('flash-message');
        notice.innerText = "戻り先が別オリジンです。戻るにはブラウザの戻るボタンを使用してください。";
        notice.innerText += "\nページ上部のメニューからトップページなどに移動できます。";
        document.body.appendChild(notice);
        console.log('Notice!');
    }
});
