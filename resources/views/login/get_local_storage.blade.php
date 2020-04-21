<body>
    <script>
        (function() {
            alert("test");
            //var origin = 'http://B.com';
            window.addEventListener('message', function(event) {
                alert(event.origin);
                var message = event.data;

                // メッセージが'get'ならlocalStorageの値を返す
                if(message === 'get') {
                    var storageData = localStorage.getItem('test');
                    event.source.postMessage(storageData, event.origin);
                }
            });
        })();
    </script>
</body>
