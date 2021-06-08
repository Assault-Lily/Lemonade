// 読み込み中に画面を隠すやつ
document.querySelectorAll('a:not([target]):not(.sf-dump-toggle)').forEach((el)=>{
    el.addEventListener('click', (e)=>{
        if(!e.ctrlKey && !e.shiftKey) document.body.classList.add('hide');
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

const showMessage = (message) => {
    document.querySelectorAll('.flash-message').forEach((el) => {
        el.remove();
    });
    let notice = document.createElement('div');
    notice.classList.add('flash-message');
    notice.innerText = message;
    document.body.appendChild(notice);
}

const getParentDir = (pathname, n) => {
    return pathname.replace(new RegExp("(?:\\\/+[^\\\/]*){0," + (n || 0) + "}$"), "");
};

const pbb = document.getElementById('pageBackButton');
if(pbb !== null){
    document.getElementById('pageBackButton').addEventListener('click',(event)=>{
        event.preventDefault();
        let backed = false;
        window.onbeforeunload = function (){
            document.body.classList.add('hide');
            backed = true;
        };
        if(document.referrer.startsWith(location.origin)){
            history.back();
            setTimeout(()=>{
                if(!backed) location.href = location.origin + getParentDir(location.pathname, 1);
            }, 200);
        }else{
            location.href = location.origin + getParentDir(location.pathname, 1);
        }
    });
}

const changeGetParam = (attribute, value, chainUrl = null) => {
    const params = (chainUrl === null) ? (new URL(document.location)).searchParams : (new URL(chainUrl).searchParams);
    if(params.has(attribute)){
        params.delete(attribute);
    }
    if(value !== null){
        params.append(attribute, value);
    }

    let retVal = location.href.split('?')[0];
    if(params.toString().length !== 0){
        retVal = retVal+'?'+params.toString();
    }

    return retVal;
};

const copyString = (string) => {
    if(window.isSecureContext === false)
        showMessage('安全でないコンテキストのため、クリップボードにコピーできませんでした。');

    navigator.clipboard.writeText(string).then(()=>{
        console.info('Text copied : ' + string);
        showMessage('クリップボードにコピーしました。');
    }, (err)=>{
        console.error('Failed to copy text. : ' + err);
        showMessage("クリップボードにコピーできませんでした。\n開発者ツールから詳細を確認できます。");
    })
};
