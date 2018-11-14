var file;
var allowedExtensions = /(\.jpg|\.jpeg)$/i;

var progress = document.querySelector('progress');

document.getElementById('confirm').addEventListener('click', e => {
    file = document.getElementById('upload').files[0];
    let filePath = document.getElementById('upload').value;
    if(!allowedExtensions.exec(filePath)){
        alert('Please upload file having extensions .jpeg/.jpg only.');
    }
    else {

        var fd = new FormData();
        
        fd.append("file", file);
        
        // These extra params aren't necessary but show that you can include other data.
        fd.append("username", "Groucho");
         //fd.append("accountnum", 123456);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'https://dev-21404557.users.info.unicaen.fr/devoir-idc2017/?o=image&a=upload', true);
        
        xhr.upload.addEventListener('progress', e => {
            if(e.lengthComputable && file != null)
              progress.setAttribute('value',(e.loaded/e.total)*100);
        });

        xhr.addEventListener('readystatechange', function(e) {
            if(xhr.readyState == 4 && file != null) {
                document.location.href="https://dev-21404557.users.info.unicaen.fr/devoir-idc2017/?o=image&a=metadata&url="+file.name;
            }
        });

        xhr.send(fd); 
    }   
});