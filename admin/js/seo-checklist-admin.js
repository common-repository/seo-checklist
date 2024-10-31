(function( $ ) {
	'use strict';
        
	var scClickedButton,
        scList,
        scContainer,
        scLoading,
        scLoadingBar,
        scPasses = 0,
        scFails = 0,
        scWarnings = 0,
        scInfos = 0;

        $(document).ready(function ($) {
            
            scContainer = $('.seo-checklist-container');
            scList = $('.seo-checklist-list');
            scLoading = $('.seo-checklist-loading');
            scLoadingBar = $('.seo-checklist-loading-bar');

            $(".seo-checklist-button").on('click', function () {
                scClickedButton = $(this);
                scBeforeChecks();
                scShowChecklist();
            });
            
        });

        function scBeforeChecks() {
            scList.empty();
            $('.seo-checklist-button').prop('disabled', true);
            scLoading.css('display', 'block');
            $('.seo-checklist-panel').hide();
            resetResultCounter();
            
            //Trigger afterRunningChecks() after all checks are done
            $(document).one("ajaxStop", function() {
                afterRunningChecks();
               
            });
        }

        /**
         * Retrieve array with the current checks
         */
        function scShowChecklist() {
            $.post(params.ajaxurl, {action: 'SEOCHECKLIST_return_seo_checks'})
            .done(function (data) {
                var checks = JSON.parse(data);
                startChecks(checks);
                scLoadingBar.css({width: '50%'});
            });
        }
        
        /**
         * Trigger checks
         */
        function startChecks(checks) {
            $.each(checks, function(index, value) {
                sendAjaxCheck(value);
            });
        }

        /**
         * Action after checks completed
         */
        function afterRunningChecks() {
            $('.seo-checklist-list-item-empty').remove();
            scCheckIfEmpty();
            $('.seo-checklist-button').prop('disabled', false);
            scLoading.css('display', 'none');
            scLoadingBar.css('width', '0%');
            $('.seo-checklist-panel').show();
            $('.seo-checklist-count--passed .number').text(scPasses);
            $('.seo-checklist-count--failed .number').text(scFails);
            $('.seo-checklist-count--warning .number').text(scWarnings);
            $('.seo-checklist-count--info .number').text(scInfos);
            $('.seo-checklist-list-item').hide();
            $('.seo-checklist-list-item--passed').show();
            $('.seo-checklist-count').removeClass('active');
            $('.seo-checklist-count--passed').addClass('active');
            
            addResultCountFilter('passed');
            addResultCountFilter('failed');
            addResultCountFilter('warning');
            addResultCountFilter('info');
        }

        /**
         * Send an ajax post with appropriate action
         * @param action
         */
        function sendAjaxCheck(action) {
            $.post(params.ajaxurl, {action: action})
            .done(function(data) {
                processAjaxResponse(data);
                scLoadingBar.css({width: '100%'});
            })
            .fail(function() {
                console.log('Check \'' + action + '\' did not succeed');
            })
            
        }

        /**
         * Retrieve ajax response and add listitem
         * @param response
         */
        function processAjaxResponse(response) {
            response = JSON.parse(response);

            if (response.debug.length > 0) {
                console.log(response.debug);
            }

            if (response.location == 'list') {

                switch(response.status) {
                    case 'passed':
                        scPasses++;
                        break;
                    case 'warning':
                        scWarnings++;
                        break;
                    case 'info':
                        scInfos++;
                        break;    
                    default:
                        scFails++;
                }
               
                if(response.messages['message'] === ''){
                    appendToList(response.status, response.messages['title'] + "</span>");
                } else {
                    appendToList(response.status, response.messages['title'] + "</span><span class='message'>" + response.messages['message'] + "</span>");
                }
            }
        }

        /**
         * Append listitem to scContainer
         * @param status passed|failed|warning
         * @param content Message for user
         */
        function appendToList(status, content) {
            switch (status) {
                case 'passed':
                    scList.append('<li class="seo-checklist-list-item seo-checklist-list-item--passed"><span class="title">' + content + '</span></li>');
                    break;
                case 'failed':
                    scList.append('<li class="seo-checklist-list-item seo-checklist-list-item--failed"><span class="title">' + content + '</span></li>');
                    break;
                case 'warning':
                    scList.append('<li class="seo-checklist-list-item seo-checklist-list-item--warning"><span class="title">' + content + '</span></li>');
                    break;
                case 'info':
                    scList.append('<li class="seo-checklist-list-item seo-checklist-list-item--info"><span class="title">' + content + '</span></li>');
                    break;
            }
        }
        
        /**
         * Check if a status has no content
         */
        function scCheckIfEmpty(){
            if (scPasses === 0) {
                scList.append('<li class="seo-checklist-list-item-empty seo-checklist-list-item seo-checklist-list-item--passed"><span>This is not good! You have no passed tests!</span>');
            }
            if (scWarnings === 0) {
                scList.append('<li class="seo-checklist-list-item-empty seo-checklist-list-item seo-checklist-list-item--warning"><span>Congratulations! You have no warnings!</span>');
            }
            if (scFails === 0) {
                scList.append('<li class="seo-checklist-list-item-empty seo-checklist-list-item seo-checklist-list-item--failed"><span>Congratulations! You have no failed tests!</span>');
            }
        }

        /**
         * Add filters
         * @param status
         */
        function addResultCountFilter(status) {
            $('.seo-checklist-count--' + status).on('click', function() {
                $('.seo-checklist-count').removeClass('active');
                $(this).addClass('active');
                $('.seo-checklist-list-item').hide();
                $('.seo-checklist-list-item--' + status).show();
            });
        }

        /**
         * Reset counters
         */
        function resetResultCounter() {
            scFails = 0;
            scPasses = 0;
            scWarnings = 0;
            scInfos = 0;
        }

})( jQuery );
