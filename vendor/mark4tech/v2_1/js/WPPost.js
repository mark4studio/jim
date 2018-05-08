/**
Jim - MVC wordpress plugin development framework
Version:           2.1
Copyright (C) 2018  Naveen

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */
 
jQuery(function($){
	$('#post').on('submit', function(e){
		var postForm = $(this);
		if(true !== postForm.data('validated')){
			var formData = postForm.serialize();
			if(/action=(.[^&]*)/g.test(formData)){
				formData = formData.replace(/action=(.[^&]*)/g, 'action=' + 'validatepost_'+encodeURIComponent(WPPost.postType));
			}
			else{
				formData += '&action=validatepost_' + encodeURIComponent(WPPost.postType);
			}
			$.ajax({
			    type: "POST",
			    url: ajaxurl,
			    data: formData,
			    dataType: "json",
			    success: function(data) {
					if(data.success){
						postForm.data('validated', true);
						postForm.submit();
					}
					else{
						for(var key in data.errors){
							alert(data.errors[key][0]);
							break;
						}
					}
			    },
			    error: function() {
			        alert('An error occured in plugin \'' + WPPost.pluginName + '\'. Please contact plugin developer');
			    }
			});
			event.preventDefault();
			return false;
		}
	});
});