function keta(num) {
    var ret;
    if (10 > num) {
        ret = "0" + num;
    } else {
        ret = num;
    }
    return ret;
}
function monthName(num) {
    const months = ["JAN",
        "FEB",
        "MAR",
        "APR",
        "MAY",
        "JUN",
        "JUL",
        "AUG",
        "SEP",
        "OCT",
        "NOV",
        "DEC"];
    if( num > 11 ){
        return false;
    }else{
        return months[num];
    }
}

function showClock() {
    const nowTime = new Date(Date.now() + ((new Date().getTimezoneOffset() + (9 * 60)) * 60 * 1000));
    document.getElementById("date-month").innerText = monthName(nowTime.getMonth());
    document.getElementById("date-date").innerText = keta(nowTime.getDate());
    document.getElementById("clock").innerText = keta(nowTime.getHours()) + ":" + keta(nowTime.getMinutes());

    let percent = (nowTime.getMinutes() + nowTime.getHours() * 60) / (60 * 24);
    document.getElementById("clock-value").setAttribute('style', 'width:'+(percent * 100)+'%');
}
document.addEventListener('DOMContentLoaded',()=>{
    showClock();
    setInterval('showClock()',1000);
});

