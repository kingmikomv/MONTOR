<!DOCTYPE html>
<html lang="en">

<x-head />

<!-- 🔥 JSTREE -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />

<body>

    <x-navbar />


    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">

                <h4 class="mb-4 fw-bold">Hirarki Jaringan</h4>

                <!-- 🔍 SEARCH -->
                <div class="mb-3">
                    <input type="text" id="search" class="form-control" placeholder="Cari pelanggan...">
                </div>

                <!-- 🌳 TREE -->
                <div id="tree" style="background:#fff; padding:15px; border-radius:10px;"></div>

            </div>
        </div>
    </div>

    <x-end />

    <!-- 🔥 SCRIPT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

    <script>
        $(function () {

            // 🌳 INIT TREE (AJAX)
            $('#tree').jstree({
                'core': {
                    'data': {
                        "url": "{{ url('/hirarki/data') }}",
                        "dataType": "json"
                    }
                },
                "plugins": ["search"]
            });

            // 🔍 SEARCH
            let to = false;
            $('#search').keyup(function () {
                if (to) clearTimeout(to);
                to = setTimeout(function () {
                    let v = $('#search').val();
                    $('#tree').jstree(true).search(v);
                }, 300);
            });

            // 🔥 CLICK NODE
            $('#tree').on("select_node.jstree", function (e, data) {

                let id = data.node.id;

                // 👤 pelanggan → detail
                if (id.startsWith("pelanggan_")) {
                    let pelangganId = id.replace("pelanggan_", "");
                    window.location.href = "/pelanggan/" + pelangganId;
                }

                // 📍 odp → map
                if (id.startsWith("odp_")) {
                    let odpId = id.replace("odp_", "");
                    window.location.href = "/map?odp_id=" + odpId;
                }

            });

        });
    </script>

</body>

</html>