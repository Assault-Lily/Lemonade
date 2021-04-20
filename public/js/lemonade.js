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
