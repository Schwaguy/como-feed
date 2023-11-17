(function() {
    tinymce.PluginManager.add('comoFeedButton', function( editor, url ) {
        editor.addButton( 'comoFeedButton', {
            text: tinyMCE_feed.button_name,
            icon: false,
            onclick: function() {
				
				var templateOptions = jQuery.parseJSON(tinyMCE_feed.feed_template_select_options);
				var categoryOptions = jQuery.parseJSON(tinyMCE_feed.feed_category_select_options);
				
				editor.windowManager.open( {
					title: tinyMCE_feed.button_title,
					body: [
                        
						{
                            type   : 'combobox',
                            name   : 'feedType',
                            label  : 'Feed Type',
                            values : [
                                { text: 'WordPress Posts', value: 'wpposts' },
                                { text: 'External IR Feed', value: 'irfeed' },
								{ text: 'External XML Feed', value: 'irfeed-xml' }
                            ]
                        },
						{
                            type   : 'listbox',
                            name   : 'category',
                            label  : 'Post Category',
                            values : categoryOptions
                        },
						{
                            type: 'textbox',
                            name: 'id',
                            label: 'ID',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'class',
                            label: 'Class',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'limit',
                            label: 'Number of items to Show',
                            value: ''
                        },
						{
                            type   : 'checkbox',
                            name   : 'excerpt',
                            label  : 'Excerpt',
                            text   : 'Show Excerpt',
                            checked : false
                        },
						{
                            type: 'textbox',
                            name: 'length',
                            label: 'Excerpt Length',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'link',
                            label: 'Link to Full Feed',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'linkTitle',
                            label: 'Link Text',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'clientid',
                            label: 'IR Client ID',
                            value: ''
                        },
						{
                            type   : 'listbox',
                            name   : 'template',
                            label  : 'Template',
                            values : templateOptions
                        },
                    ],
					
					//[feed-widget id='' class='' feedType='' limit='' category='' excerpt=true/false length='' link='' link-title='' clientid='' template='']
					
                    onsubmit: function( e ) {
						
						var feedID = (e.data.id ? ' id='+e.data.id : '');
						var feedClass = (e.data.id ? ' id='+e.data.id : '');
						var feedType = (e.data.feedType ? ' feed-type='+e.data.feedType : ' feed-type=wpposts');
						var limit = (e.data.limit ? ' limit='+e.data.limit : '');
						var category = (e.data.category ? ' category='+e.data.category : '');
						var excerpt = (e.data.excerpt ? ' excerpt=true' : '');
						var length = (e.data.length ? ' length='+e.data.length : '');
						var link = (e.data.link ? " link='"+e.data.link+"'" : '');
						var linkTitle = (e.data.linkTitle ? " link-title='"+e.data.linkTitle+"'" : '');
						var clientid = (e.data.clientid ? " clientid='"+e.data.clientid+"'" : '');
						var template = (e.data.template ? e.data.template : 'default');
						
                        editor.insertContent( '[como-feed template='+ template + feedType + feedID + feedClass + limit + category + excerpt + length + link + linkTitle + clientid +']');
                    }
                });
            },
        });
    });

})();