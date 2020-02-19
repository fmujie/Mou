$(document).ready(function () {
    $('body').append('<span id="msg">PHP</span>');
    $(document).mousemove(function (e) {
        document.onclick = function on() {
            var wz = document.getElementById('msg');
            wz.style.display = "inline-block";
            wz.style.left = e.pageX + "px";
            wz.style.top = e.pageY + "px";
            var sjfh = [];
            sjfh[0] = "爱你呦";
            sjfh[1] = "打你啦";
            sjfh[2] = "瞅瞅你";
            sjfh[3] = "喜欢你";
            sjfh[4] = "一直陪你呦";
            sjfh[5] = "PHP";
            sjfh[6] = "JavaScript";
            sjfh[7] = "IOS";
            sjfh[8] = "Web";
            sjfh[9] = "python";
            sjfh[10] = "Android";
            var randomfh = Math.round(Math.random() * 10);
            document.getElementById("msg").innerText = (sjfh[randomfh]);
            setTimeout(function () {
                var wz = document.getElementById('msg');
                wz.style.display = "none";
            }, 1500);
        }
    });
});
