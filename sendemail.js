function block_helpdesk_sendemail(e, args) {
    
    e.preventDefault();

    var sesskey, courseid, context;

    var ioconfig = {
        method: 'GET',
        data: {"sesskey": M.cfg.sesskey, "courseid": encodeURIComponent(args.courseid), 
                      "context": encodeURIComponent(args.context)},
        on: {
            success: function (o, response) {

              var data;
              Y.log(response.responseText);

              try {

                data = Y.JSON.parse(response.responseText);

              } catch (e) {

                Y.log("JSON Parse failed!" + e);
                return;

              }
              if (data.result) {

                //remove existing div nodes
                var nodes = Y.one('.content1');
                nodes.all('div').remove();
                //show hidden nodes
                var new_node = Y.one('#helpdesk_success');
                new_node.show();

              }
            },
            failure: function (o, response) {

              Y.log('Failure' + response.responseText);
              
              //remove existing div nodes
              var nodes = Y.one('.content1');
              nodes.all('div').remove();
              //show hidden nodes
              var new_node = Y.one('#helpdesk_failure');
               new_node.show();

            }
         }
    };

    Y.io(M.cfg.wwwroot + '/blocks/helpdesk/sendmail.php', ioconfig);
}