// 読み込み中に画面を隠すやつ
document.querySelectorAll('a:not([target]):not(.sf-dump-toggle)').forEach((el)=>{
    el.addEventListener('click', (e)=>{
        if(!e.ctrlKey && !e.shiftKey) document.getElementsByTagName('body')[0].classList.add('hide');
    });
});

