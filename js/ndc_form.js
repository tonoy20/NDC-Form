$(document).ready(function(){
    $image_crop = $('#image_demo').croppie({
       enableExif: true,
       viewport: {
         width:200,
         height:200,
         type:'circle' //circle
       },
       boundary:{
         width:300,
         height:300
       }
     });
     $('#s_image').on('change', function(){
       var reader = new FileReader();
       reader.onload = function (event) {
         $image_crop.croppie('bind', {
           url: event.target.result
         }) 
       }
       reader.readAsDataURL(this.files[0]);
       $('#uploadimage').show();
     });
     $('.crop_image').click(function(event){
       event.preventDefault();
       $image_crop.croppie('result', {
         type: 'canvas',
         size: 'viewport'
       }).then(function(response){
         $.ajax({
           url: ajax_object.ajax_url,
           type: "POST",
           data:{
               action: "crop_img",
               "image1": response
           },
           success:function(data)
           {
               html = '<img src="' + response + '" />';
               $('#uploaded_image').html(html);
           }
         });
       })
     });
   });
   
function onSubmit(token) {
    if(jQuery('#name-f').val() != '' && jQuery('#tel').val() != '' && jQuery('#email').val() != '' && jQuery('#c_roll_number').val() != '' && jQuery('#c_group').val() != '' && jQuery('#address-1').val() != '' && jQuery('#employ_type').val() != '' && jQuery('#current_position').val() != '' && jQuery('#medium').val() != '' && jQuery('#batch-year').val() != ''){
        document.getElementById("ndc-form").submit();
    }else{
        alert('Please fill the required fields.');
    }
}
   