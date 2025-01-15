<script type="text/javascript">
    function view_document(file_name='',file_type='',type=''){
        if(type == 'user_document' && file_type == 'image'){
            var url = "{{asset('storage/app/public/uploads/document/users')}}";
            var img = '<img src="'+url+'/'+file_name+'" class="img-rounded" width="220px" height="230px">';

            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';

            $('#document_file_set').html(img);
            $('#document_file_name').html('File Name :-'+button);
            $("#viewUserDocumentModal").modal();
        }
        else if(type == 'user_document' && file_type == 'pdf'){
            var url = "{{asset('storage/app/public/uploads/document/users')}}";
            var img = '<embed id="pdfEmbed" src="'+url+'/'+file_name+'" width="100%" height="500px" type="application/pdf">'
            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';

            $('#document_file_set').html(img);
            $('#document_file_name').html('File Name :-'+button);
            $("#viewUserDocumentModal").modal();
        }
        else{
            var url = "{{asset('storage/app/public/uploads/document/users')}}";
            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';
            $('#document_file_name').html('File Name :-'+button);
            $("#viewUserDocumentModal").modal();
        }
        
                    
        return false;
    }
</script>