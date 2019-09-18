var baidumap = {
    ruleObj:{},
    init:function(opt){
        var map = new BMap.Map("baidumap",{enableMapClick:false});              // 创建Map实例
        var geo = new BMap.Geocoder();
        if(!$.isEmptyObject(opt)){
            map.centerAndZoom(new BMap.Point(parseFloat(opt.lng), parseFloat(opt.lat)),parseInt(opt.zoom));  //初始化时，即可设置中心点和地图缩放级别。
            createMarker(opt);
            baidumap.ruleObj.lng = opt.lng;
            baidumap.ruleObj.lat = opt.lat;
            baidumap.ruleObj.zoom = opt.zoom;
        }else{
            map.centerAndZoom('深圳',12);                 //初始化时，即可设置中心点和地图缩放级别。
        }
        
        map.enableScrollWheelZoom();
        map.addControl(new BMap.NavigationControl());

        function setPlace(value){
            map.clearOverlays();                        //清除地图上所有覆盖物
            function myFun(){
                var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
                map.centerAndZoom(pp, 18);
                createMarker(pp);
                baidumap.ruleObj.lng = pp.lng;
                baidumap.ruleObj.lat = pp.lat;
                baidumap.ruleObj.zoom = map.getZoom();
            }
            var local = new BMap.LocalSearch(map, {             //智能搜索
                onSearchComplete: myFun
            });
            local.search(value);
        }

        function createMarker(pp){
        	var marker = new BMap.Marker(pp);
            map.addOverlay(marker);  
        	marker.enableDragging();
            marker.addEventListener('dragend',function(e){
                geo.getLocation(e.point, function(rs){
                    var addComp = rs.addressComponents;
                    $('#baiduvalue').val(addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber);
                });
                baidumap.ruleObj.lng = e.point.lng;
                baidumap.ruleObj.lat = e.point.lat;
                baidumap.ruleObj.zoom = map.getZoom();
            });
        }

        $('#locate').click(function(){
            var value = $.trim($('#baiduvalue').val());
            if(value){
                setPlace(value);
            }
        })
    }
}

;(function($) {
	//插入图片插件
	$.fn.InsertImage = function(options){
		var defaults = {}
		var opts = $.extend({}, defaults, options);

		var html = [];
		html.push('<div class="LocalImage">');
		html.push('<div class="tab-content">');
		html.push('<ul class="nav nav-tabs">');
		html.push('<li class="active"><a href="#image-space" data-toggle="tab">图片空间</a></li>');
		html.push('<li><a href="#upload-image" data-toggle="tab">上传图片</a></li>');
		html.push('<li><a href="#network-image" data-toggle="tab">网络图片</a></li>');
		html.push('</ul>');
		html.push('<div class="tab-pane active" id="image-space"></div>');
		html.push('<div class="tab-pane upload-image" id="upload-image">');
		html.push('<input type="file" name="file" style=" display:none;" id="fileInput" ><div class="upload_cnt"><p><i class="icon-laptop"></i><br/>点击上传图片</p></div>');
		html.push('</div>');
		html.push('<div class="tab-pane network-image" id="network-image" >');
		html.push('<div class="col-sm-2 no-padding" style="line-height:32px;">图片网址：</div>');
		html.push('<div class="col-sm-10 no-padding"><input type="text" id="netImageUrl" class="col-xs-12"></div>');
		html.push('</div>');
		html.push('</div>');
		html.push('</div>');

		var InsertImage = function(){
			var _this = this;
			var dialog = bootbox.dialog({
		        message:html.join(''),
		        show:!1,
		        backdrop: !0,
		        width:'720px',
		        buttons: 			
				{
					"danger" :
					{
						"label" : "取消",
					},
					"success" :
					{
						"label" : "<i class='icon-ok'></i>确认",
						"className" : "btn-primary",
						callback: function() {
							var index = $('.LocalImage .nav li.active').index();
							if(index == 0){
								setImage($('#image-space .imgshow').find("li.on img").attr('src'));
								return true;
							}else if(index == 2){
								var netImageUrl = $('#netImageUrl').val();
								if(!netImageUrl){
									alert('请输入图片网址');
									return false;
								}
								setImage(netImageUrl);
								return true;
							}
							return false;
						}
					}
				}
		    });
			dialog.modal("show");

			$('#upload-image p').click(function(){
				var $this = $(this);
				document.getElementById('fileInput').click();
		        $('#fileInput').unbind('change').change(function(e){
		            var file = e.target.files || e.dataTransfer.files;
		            if (file[0].type && /\.(?:jpg|jpeg|png|gif)$/.test(file[0].name)) {
		                var xhr = new XMLHttpRequest();
		                if (xhr.upload) {
		                	$this.before('<img class="loading" src="template/skin/images/loading.gif" /><br/>上传中').remove();
		                    xhr.onreadystatechange = function(e) {
		                        if (xhr.readyState == 4) {
		                            if(xhr.status == 200) {
		                                var msg = JSON.parse(xhr.responseText);
		                                if(msg.status == 1){
		                                	setImage(msg.message);
		                                	dialog.modal("hide");
		                                }else{
		                                	alert(msg.message);
		                                }
		                            }else {
		                                alert('上传失败');
		                            }
		                        }
		                    };
		                    // 开始上传
		                    xhr.open('POST', __APP__+'/index.php?m=media_AjaxUpload', true);
		                    var formData = new FormData();
		                    formData.append('file', file[0]);
		                    formData.append('ajax', true);
		                    if(opts.wid){
		                    	formData.append('wid', opts.wid);
		                    }
		                    xhr.send(formData);
		                }
		            }else{
		                PopAlert('格式不正确,请重新选择','error');
		            }
		        });
			});

			getImageList(1);

			function setImage(path){
				if($.isFunction(opts.callback)){
					opts.callback.call(_this,path);
				}else{
					$(_this).siblings('img').attr('src',path);
				}
				
			}

			function getImageList(page){
				page = page ? page : 1;
				$.ajax({
					type: "POST",
					url: __APP__+"/index.php?m=media_AjaxGetImageList",
					data: "pageSize=21&page="+page+"&wid="+opts.wid,
					dataType: "json",
					success: function(msg){
						if(msg.status == 1){
							buildHtml(page,msg);
						}else{
							$('#image-space').html('<p style=" text-align:center; padding-top:170px;">暂无图片,请选择上传图片</p>');
						}
					}
				});
			}

			function buildHtml(page,obj){
				var imglist = obj.list,
		        	count = obj.count,
		        	html = [];
		        html.push('<ul class="imgshow">');
		        $.each(obj.list,function(i,o){
		        	html.push('<li><img src="'+o.url+'" /></li>');
		        });
		        html.push('</ul>');
		        
		        if(count > 21){
		        	html.push('<ul class="pagination" >');
		        	for (var i = 1; i <= Math.ceil(count/21); i++) {
		        		if(page == i){
		        			html.push('<li class="active"><a href="javascript:;">'+i+'</a></li>');
		            	}else{
		            		html.push('<li data-page="'+i+'"><a href="javascript:;">'+i+'</a></li>');
		            	}
		          	}
		        	html.push('</ul>');
		        }
		        $('#image-space').html(html.join(''));

		        $('#image-space .imgshow').find("li").click(function(){
		        	$(this).addClass('on').siblings().removeClass('on');
		        });
		        
		        $('#image-space .pagination').find("li").click(function(){
		        	getImageList($(this).data('page'));
		        });
			}
		};

		if(opts.el == undefined){
			return this.click(InsertImage);
		}else{
			return this.on('click',opts.el,InsertImage);
		}
	}

	//选择图文插件
	$.fn.ChoiceNews = function(options){
		var defaults = {}
		var opts = $.extend({}, defaults, options);
		var html = [];
		html.push('<div class="newsbox">');
		html.push('<div class="newsheader">选择图文</div>');
		html.push('<div class="newsbody"></div></div>');

		$('.relation').on('click','span',function(){
			var relation = decodeURIComponent($('.relation').attr('obj')),
				$this = $(this).closest('li');
			relation = relation && relation != 'undefined'  ? JSON.parse(relation) : [];
			relation.splice($this.index(),1);
			$('.relation').attr('obj',encodeURIComponent(JSON.stringify(relation)));
			$this.remove();
		})

		return this.click(function(){
			var _this = this;
			var dialog = bootbox.dialog({
		        message:html.join(''),
		        show:!1,
		        backdrop: !0,
		        width:'500px',
		        buttons: 			
				{
					"danger" :
					{
						"label" : "取消",
						"className" : "btn"
					},
					"success" :
					{
						"label" : "<i class='icon-ok'></i>确认",
						"className" : "btn btn-primary",
						callback: function() {
							var relation = decodeURIComponent($('.relation').attr('obj')),
								html = [];
							relation = relation && relation != 'undefined'  ? JSON.parse(relation) : [];

							$('.newsbody').find('input[type="checkbox"]:checked').each(function(){
								var isthere = false;
								if(relation.length){
									for (var i in relation) {
										if(relation[i].id == this.value){
											isthere = true;
											break;
										}
									};
									if(isthere){
										return true;
									}
								}
								var temp = {};
								temp.id = this.value;
								temp.text = $(this).siblings('span').text();
								relation.push(temp);
							});

							if(relation.length > 8){
								alert('最多只能添加8条图文');
								return false;
							}

							$.each(relation,function(i,o){
								html.push('<li><span>删除</span>'+o.text+'</li>');
							});

							$('.relation').html(html.join('')).attr('obj',encodeURIComponent(JSON.stringify(relation))).show();
						}
					}
				}
		    });
			dialog.modal("show");

			getNewsList();

			function getNewsList(page){
				page = page ? page : 1;
				$.ajax({
					type: "GET",
					url: __APP__+"/index.php?m=reply_getNewsList",
					data: "pageSize=10&page="+page+"&wid="+opts.wid,
					dataType: "json",
					success: function(msg){
						if(msg.status == 1){
							buildHtml(page,msg);
						}else{
							alert(msg.message);
						}
					}
				});
			}

			function buildHtml(page,obj){
				var imglist = obj.list,
		        	count = obj.count,
		        	html = [];
		        html.push('<ul class="newlist">');
		        $.each(obj.list,function(i,o){
		        	var temp = JSON.parse(o.mess_content);
		        	html.push('<li><label><input type="checkbox" value="'+o.mid+'" class="ace"><span class="lbl">'+temp.title+'</span></label></li>');
		        });
		        html.push('</ul>');
		        
		        if(count > 10){
		        	html.push('<ul class="pagination" >');
		        	for (var i = 1; i <= Math.ceil(count/10); i++) {
		        		if(page == i){
		        			html.push('<li class="active"><a href="javascript:;">'+i+'</a></li>');
		            	}else{
		            		html.push('<li data-page="'+i+'"><a href="javascript:;">'+i+'</a></li>');
		            	}
		          	}
		        	html.push('</ul>');
		        }
		        $('.newsbody').html(html.join(''));
		        
		        $('.newsbody .pagination').find("li").click(function(){
		        	getNewsList($(this).data('page'));
		        });
			}
		});
	}

	//选择图标插件
	$.fn.InsertIcon = function(options){
		var list = ["icon-file-text", "icon-globe ", "icon-credit-card", "icon-hand-up", "icon-dashboard ", "icon-money ", "icon-reorder", "icon-comments-alt ", "icon-smile", "icon-thumbs-up", "icon-truck", "icon-shopping-cart", "icon-group", "icon-user-md", "icon-home", "icon-plane", "icon-gift", "icon-food", "icon-phone", "icon-tags", "icon-rocket", "icon-cloud", "icon-map-marker", "icon-music", "icon-trophy", "icon-android", "icon-apple", "icon-star", "icon-rss-sign", "icon-heart", "icon-envelope", "icon-bar-chart", "icon-picture", "icon-download", "icon-gamepad", "icon-comment", "icon-check", "icon-cog", "icon-camera", "icon-cloud", "icon-facetime-video", "icon-spinner", "icon-bullhorn", "icon-location-arrow", "icon-list-ul", "icon-weibo", "icon-windows", "icon-time", "icon-th", "icon-user", "icon-microphone", "icon-bookmark","icon-flag-checkered", "icon-qrcode", "icon-glass", "icon-stethoscope", "icon-medkit", "icon-ambulance", "icon-hospital", "icon-foursquare", "icon-download-alt", "icon-coffee", "icon-building", "icon-edit", "icon-book", "icon-question-sign", "icon-legal", "icon-calendar-empty", "icon-ellipsis-horizontal", "icon-pencil", "icon-suitcase", "icon-warning-sign", "icon-jpy", "icon-list-alt", "icon-html5", "icon-gittip", "icon-search", "icon-wrench", "icon-lemon", "icon-indent-right", "icon-paste", "icon-archive", "icon-sun", "icon-bitbucket"];
		var list_all = ["icon-compass", "icon-collapse", "icon-collapse-top", "icon-expand", "icon-file", "icon-file-text", "icon-thumbs-up", "icon-thumbs-down", "icon-xing", "icon-xing-sign", "icon-youtube-play", "icon-dropbox", "icon-stackexchange", "icon-instagram", "icon-flickr", "icon-adn", "icon-bitbucket-sign", "icon-tumblr", "icon-tumblr-sign", "icon-long-arrow-down", "icon-long-arrow-up", "icon-long-arrow-left", "icon-long-arrow-right", "icon-apple", "icon-android", "icon-skype", "icon-foursquare", "icon-trello", "icon-female", "icon-gittip", "icon-sun", "icon-moon", "icon-archive", "icon-vk", "icon-weibo", "icon-renren", "icon-adjust", "icon-anchor", "icon-archive", "icon-asterisk", "icon-ban-circle", "icon-bar-chart", "icon-barcode", "icon-beaker", "icon-beer", "icon-bell", "icon-bell-alt", "icon-bolt", "icon-book", "icon-bookmark", "icon-bookmark-empty", "icon-briefcase", "icon-bug", "icon-building", "icon-bullhorn", "icon-bullseye", "icon-calendar", "icon-calendar-empty", "icon-camera", "icon-camera-retro", "icon-certificate", "icon-check", "icon-check-empty", "icon-check-minus", "icon-check-sign", "icon-circle", "icon-circle-blank", "icon-cloud", "icon-cloud-download", "icon-cloud-upload", "icon-code", "icon-code-fork", "icon-coffee", "icon-cog", "icon-cogs", "icon-collapse", "icon-collapse-alt", "icon-collapse-top", "icon-comment", "icon-comment-alt", "icon-comments", "icon-comments-alt", "icon-compass", "icon-credit-card", "icon-crop", "icon-dashboard", "icon-desktop", "icon-download", "icon-download-alt", "icon-edit", "icon-edit-sign", "icon-ellipsis-horizontal", "icon-ellipsis-vertical", "icon-envelope", "icon-envelope-alt", "icon-eraser", "icon-exchange", "icon-exclamation", "icon-exclamation-sign", "icon-expand", "icon-expand-alt", "icon-external-link", "icon-external-link-sign", "icon-eye-close", "icon-eye-open", "icon-facetime-video", "icon-female", "icon-fighter-jet", "icon-film", "icon-filter", "icon-fire", "icon-fire-extinguisher", "icon-flag", "icon-flag-alt", "icon-flag-checkered", "icon-folder-close", "icon-folder-close-alt", "icon-folder-open", "icon-folder-open-alt", "icon-food", "icon-frown", "icon-gamepad", "icon-gear", "icon-gears", "icon-gift", "icon-glass", "icon-globe", "icon-group", "icon-hdd", "icon-headphones", "icon-heart", "icon-heart-empty", "icon-home", "icon-inbox", "icon-info", "icon-info-sign", "icon-key", "icon-keyboard", "icon-laptop", "icon-leaf", "icon-legal", "icon-lemon", "icon-level-down", "icon-level-up", "icon-lightbulb", "icon-location-arrow", "icon-lock", "icon-magic", "icon-magnet", "icon-mail-forward", "icon-mail-reply", "icon-mail-reply-all", "icon-male", "icon-map-marker", "icon-meh", "icon-microphone", "icon-microphone-off", "icon-minus", "icon-minus-sign", "icon-minus-sign-alt", "icon-mobile-phone", "icon-money", "icon-moon", "icon-move", "icon-music", "icon-off", "icon-ok", "icon-ok-circle", "icon-ok-sign", "icon-pencil", "icon-phone", "icon-phone-sign", "icon-picture", "icon-plane", "icon-plus", "icon-plus-sign", "icon-plus-sign-alt", "icon-power-off", "icon-print", "icon-pushpin", "icon-puzzle-piece", "icon-qrcode", "icon-question", "icon-question-sign", "icon-quote-left", "icon-quote-right", "icon-random", "icon-refresh", "icon-remove", "icon-remove-circle", "icon-remove-sign", "icon-reorder", "icon-reply", "icon-reply-all", "icon-resize-horizontal", "icon-resize-vertical", "icon-retweet", "icon-road", "icon-rocket", "icon-rss", "icon-rss-sign", "icon-screenshot", "icon-search", "icon-share", "icon-share-alt", "icon-share-sign", "icon-shield", "icon-shopping-cart", "icon-sign-blank", "icon-signal", "icon-signin", "icon-signout", "icon-sitemap", "icon-smile", "icon-sort", "icon-sort-by-alphabet", "icon-sort-by-alphabet-alt", "icon-sort-by-attributes", "icon-sort-by-attributes-alt", "icon-sort-by-order", "icon-sort-by-order-alt", "icon-sort-down", "icon-sort-up", "icon-spinner", "icon-star", "icon-star-empty", "icon-star-half", "icon-star-half-empty", "icon-star-half-full", "icon-subscript", "icon-suitcase", "icon-sun", "icon-superscript", "icon-tablet", "icon-tag", "icon-tags", "icon-tasks", "icon-terminal", "icon-thumbs-down", "icon-thumbs-down-alt", "icon-thumbs-up", "icon-thumbs-up-alt", "icon-ticket", "icon-time", "icon-tint", "icon-trash", "icon-trophy", "icon-truck", "icon-umbrella", "icon-unchecked", "icon-unlock", "icon-unlock-alt", "icon-upload", "icon-upload-alt", "icon-user", "icon-volume-down", "icon-volume-off", "icon-volume-up", "icon-warning-sign", "icon-wrench", "icon-zoom-in", "icon-zoom-out", "icon-eur", "icon-gbp", "icon-krw", "icon-renminbi", "icon-rupee", "icon-usd", "icon-yen", "icon-align-center", "icon-align-justify", "icon-align-left", "icon-align-right", "icon-bold", "icon-columns", "icon-copy", "icon-cut", "icon-eraser", "icon-file", "icon-file-alt", "icon-file-text", "icon-file-text-alt", "icon-font", "icon-indent-left", "icon-indent-right", "icon-italic", "icon-link", "icon-list", "icon-list-alt", "icon-list-ol", "icon-list-ul", "icon-paper-clip", "icon-paste", "icon-rotate-left", "icon-rotate-right", "icon-save", "icon-strikethrough", "icon-table", "icon-text-height", "icon-text-width", "icon-th", "icon-th-large", "icon-th-list", "icon-underline", "icon-unlink", "icon-angle-down", "icon-angle-left", "icon-angle-right", "icon-angle-up", "icon-arrow-down", "icon-arrow-left", "icon-arrow-right", "icon-arrow-up", "icon-caret-down", "icon-caret-left", "icon-caret-right", "icon-caret-up", "icon-chevron-down", "icon-chevron-left", "icon-chevron-right", "icon-chevron-sign-down", "icon-chevron-sign-left", "icon-chevron-sign-right", "icon-chevron-sign-up", "icon-chevron-up", "icon-circle-arrow-down", "icon-circle-arrow-left", "icon-circle-arrow-right", "icon-circle-arrow-up", "icon-double-angle-down", "icon-double-angle-left", "icon-double-angle-right", "icon-double-angle-up", "icon-hand-down", "icon-hand-left", "icon-hand-right", "icon-hand-up", "icon-backward", "icon-eject", "icon-fast-backward", "icon-fast-forward", "icon-forward", "icon-fullscreen", "icon-pause", "icon-play", "icon-play-circle", "icon-play-sign", "icon-resize-full", "icon-resize-small", "icon-step-backward", "icon-step-forward", "icon-stop", "icon-youtube-play", "icon-bitbucket", "icon-bitcoin", "icon-css3", "icon-dribbble", "icon-facebook", "icon-facebook-sign", "icon-flickr", "icon-foursquare", "icon-github", "icon-github-alt", "icon-github-sign", "icon-gittip", "icon-google-plus", "icon-google-plus-sign", "icon-html5", "icon-instagram", "icon-linkedin", "icon-linkedin-sign", "icon-linux", "icon-maxcdn", "icon-pinterest", "icon-pinterest-sign", "icon-trello", "icon-twitter", "icon-twitter-sign", "icon-windows", "icon-youtube", "icon-youtube-sign", "icon-ambulance", "icon-h-sign", "icon-hospital", "icon-medkit", "icon-plus-sign-alt", "icon-stethoscope", "icon-user-md"];
		var tmp = '<li class="tile-themed"><i class="{0}"></i></li>';
		var ul = '<ul class="icon_list">{0}</ul>';
		var html0 = [];
		var html1 = [];
		var html2 = [];
		var dialog = null;

		$.each(list, function (k, v) {
			html1.push('<li> <i class="'+v+'"></i></li>');
		});
		$.each(list_all, function (k, v) {
			html2.push('<li> <i class="'+v+'"></i></li>');
		})

		html0.push('<div class="icons-cont" style="display: block;">');
		html0.push('<div class="tab-content">');
		html0.push('<ul class="nav nav-tabs">');
		html0.push('<li class="active"><a href="#ico_hot" data-toggle="tab">热门</a></li>');
		html0.push('<li class=""><a href="#ico_all" data-toggle="tab">全部</a></li>');
		html0.push('</ul>');
		html0.push('<div class="tab-pane fade active in" id="ico_hot"><ul class="icon_list">'+html1.join('')+'</ul></div>');
		html0.push('<div class="tab-pane fade" id="ico_all"><ul class="icon_list">'+html2.join('')+'</ul></div>');
		html0.push('</div>');
		html0.push('</div>');

		this.click(function(){
			var $this = $(this);
			dialog = bootbox.dialog({
		        message:html0.join(''),
		        show:!1,
		        backdrop: !0,
		        width:'483px'
		    });
			dialog.modal("show");
			$(".icon_list").on("click",'li', function () {
				var classname = $(this).children().attr("class");
				$this.siblings('i').attr("class", classname).siblings('input').val(classname);
				dialog.modal("hide");
			});
		});
		return this;
	}
})(jQuery);