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
    var months;
    months = ["JAN",
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
    var nowTime = new Date(Date.now() + ((new Date().getTimezoneOffset() + (9 * 60)) * 60 * 1000));
    document.getElementById("date-month").innerText = monthName(nowTime.getMonth());
    document.getElementById("date-date").innerText = keta(nowTime.getDate());
    document.getElementById("clock").innerText = "JST " + keta(nowTime.getHours()) + ":" + keta(nowTime.getMinutes());
}
document.addEventListener('DOMContentLoaded',()=>{
    showClock();
    setInterval('showClock()',1000);
});

