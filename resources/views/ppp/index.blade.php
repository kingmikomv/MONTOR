<!DOCTYPE html>
<html lang="en">

<x-head />

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>

body{
background:#f4f6f9;
}

.page-title{
font-weight:700;
font-size:22px;
}

.monitor-card{
background:#fff;
border-radius:10px;
box-shadow:0 4px 12px rgba(0,0,0,0.06);
padding:20px;
}

.table{
font-size:14px;
}

.table thead th{
font-weight:600;
text-align:center;
}

.table td{
vertical-align:middle;
}

.table-hover tbody tr:hover{
background:#f8f9fb;
}

.badge{
padding:6px 12px;
font-size:12px;
border-radius:20px;
}

.top-bar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

</style>

<body>

<x-navbar />

<div class="container mt-4" style="margin-bottom:5em">

<div class="top-bar">

<div class="page-title">
Monitoring PPP Realtime
</div>

<button id="stopAlarm" class="btn btn-danger">
🔕 Matikan Alarm
</button>

</div>

<div class="row">

<!-- ===============================
OFFLINE TABLE
=============================== -->

<div class="col-lg-6 mb-4">

<div class="monitor-card">

<div class="text-danger mb-3" style="font-weight:700;">
⚠ Pelanggan Offline
</div>

<div class="table-responsive">

<table id="offlineTableDT" class="table table-hover table-bordered">

<thead class="table-danger">
<tr>
<th>Nama</th>
<th>Username</th>
<th>Status</th>
<th>Lokasi</th>
</tr>
</thead>

<tbody id="offlineTable">

@foreach($offlinePelanggan as $p)

<tr id="offline-{{ $p->username_pppoe }}">

<td>{{ $p->nama }}</td>

<td>{{ $p->username_pppoe }}</td>

<td class="text-center">
<span class="badge bg-danger">OFFLINE</span>
</td>

<td class="text-center">

<a href="https://www.google.com/maps?q={{ $p->lat }},{{ $p->lng }}"
target="_blank"
class="btn btn-primary btn-sm">

📍 Lokasi

</a>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>


<!-- ===============================
ALL CUSTOMER TABLE
=============================== -->

<div class="col-lg-6 mb-4">

<div class="monitor-card">

<div class="mb-3" style="font-weight:600;">
Semua Pelanggan
</div>

<div class="table-responsive">

<table id="pelangganTable" class="table table-hover table-bordered">

<thead class="table-dark">
<tr>
<th>Nama</th>
<th>Username</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@foreach($mapPelanggan as $p)

<tr id="row-{{ $p->username_pppoe }}"
data-lat="{{ $p->lat }}"
data-lng="{{ $p->lng }}">

<td>{{ $p->nama }}</td>

<td>{{ $p->username_pppoe }}</td>

<td class="text-center">

<span class="badge 
{{ $p->status == 'online' || $p->status == 'aktif' ? 'bg-success' : 'bg-danger' }}
status-text">

{{ strtoupper($p->status) }}

</span>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<x-end />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://unpkg.com/pusher-js@8.2.0/dist/web/pusher.min.js"></script>

<script>

let offlineDT;
let pelangganDT;

$(document).ready(function(){

pelangganDT = $('#pelangganTable').DataTable({
pageLength:10,
order:[[0,'asc']]
});

offlineDT = $('#offlineTableDT').DataTable({
pageLength:5,
order:[[0,'asc']]
});

});

document.addEventListener("DOMContentLoaded",function(){

let alarm = new Audio("https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3");

alarm.loop = true;

let alarmActive=false;
let audioReady=false;

function unlockAudio(){

if(audioReady) return;

alarm.play().then(()=>{

alarm.pause();
alarm.currentTime=0;
audioReady=true;

}).catch(()=>{});

}

document.addEventListener("click",unlockAudio,{once:true});

document.getElementById("stopAlarm").addEventListener("click",function(){

alarm.pause();
alarm.currentTime=0;
alarmActive=false;

});

if("Notification" in window){
Notification.requestPermission();
}

const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}",{
cluster:"{{ env('PUSHER_APP_CLUSTER') }}",
forceTLS:true
});

const channel = pusher.subscribe("ppp-channel");

channel.bind("PppEvent",function(e){

let row=document.getElementById("row-"+e.user);

if(!row) return;

let nama=row.children[0].innerText;
let username=row.children[1].innerText;

let statusCell=row.querySelector(".status-text");

let lat=row.dataset.lat;
let lng=row.dataset.lng;


/* =========================
USER ONLINE
========================= */

if(e.type==="add"){

statusCell.className="badge bg-success status-text";
statusCell.innerText="ONLINE";

if(offlineDT.row('#offline-'+username).length){

offlineDT.row('#offline-'+username).remove().draw(false);

}

}


/* =========================
USER OFFLINE
========================= */

if(e.type==="remove"){

statusCell.className="badge bg-danger status-text";
statusCell.innerText="OFFLINE";

if(!document.getElementById("offline-"+username)){

let newRow = $(`
<tr id="offline-${username}">
<td>${nama}</td>
<td>${username}</td>
<td class="text-center">
<span class="badge bg-danger">OFFLINE</span>
</td>
<td class="text-center">
<a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="btn btn-sm btn-primary">
📍 Lokasi
</a>
</td>
</tr>
`);

offlineDT.row.add(newRow).draw(false);

}

}


/* =========================
ALARM
========================= */

if(audioReady && !alarmActive){

alarmActive=true;
alarm.play().catch(()=>{});

}


/* =========================
NOTIFICATION
========================= */

if(Notification.permission==="granted"){

new Notification("⚠ Pelanggan Offline",{
body:nama+" ("+username+") sedang offline",
icon:"https://cdn-icons-png.flaticon.com/512/564/564619.png"
});

}

});

});

</script>

</body>
</html>