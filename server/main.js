var thr_HRT, thr_Maz, thr_Par;
function reloadMiners() {
    var time = new Date().getTime() / 1000;
    thr_HRT = 0;
    thr_Maz = 0;
    thr_Par = 0;

    $("#miners").html("");
    $("#miners").append("<tr><th>ID Minero</th><th>Hash-rate Friendly</th><th>Hash-rate no tan Friendly</th><th>Hash-rate 🔥</th><th>Estado</th></tr>");

    $.getJSON('hash-rates.json?cache=' + parseInt(time), function (jd) {
        jd.hashes.forEach(m => drawMiner(m.id, m.hr_HRT, m.hr_Maz, m.hr_Par, time - m.heartbeat < 13))

    }).always(function () { $("#miners").append("<tr><td>Total:</td><td>" + thr_HRT + " H/s</td><td>" + thr_Maz + " H/s</td><td>" + thr_Par + " H/s</td><td></td></tr>"); });;
}
function drawMiner(id, hr_HRT, hr_Maz, hr_Par, online) {
    emoji = (online) ? "🟢" : "🔴";
    thr_HRT += hr_HRT;
    thr_Maz += hr_Maz;
    thr_Par += hr_Par;
    $("#miners").append("<tr><td>" + id + "</td><td>" + hr_HRT + " H/s</td><td>" + hr_Maz + " H/s</td><td>" + hr_Par + " H/s</td><td>" + emoji + "</td></tr>");
}

function status_minado() {
    $.get("status_missioncontrol.php", function (data) {
        $("#estado").html(data);
    });

}
function tminus0() {
    $.post("green.php", { "cpu_mode": $("#cpu_mode").val(), "numceros": $("#ceros").val(), "id_pool": $("#id").val() });
}
$(document).ready(function () {
    reloadMiners();
    status_minado();
    setInterval(function () {
        reloadMiners();
    }, 9000);
    setInterval(function () {
        status_minado();
    }, 3000);



});