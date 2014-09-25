function block_helpdesk_sendemail(e, args) {
    
    Y.log('args' + args);
    e.preventDefault();

    Y.log('Enetered method');

    var sesskey, courseid, context;

    var ioconfig = {
        method: 'GET',
        data: {"sesskey": M.cfg.sesskey, "courseid": encodeURIComponent(args.courseid), 
                      "context": encodeURIComponent(args.context)},
        on: {
            success: function (o, response) {
              //OK
              debugger;
              var data;
              Y.log(response.responseText);

              try {
                data = Y.JSON.parse(response.responseText);

                Y.log("RAW JSON DATA: " + data);

              } catch (e) {
                alert("JSON Parse failed!");
                Y.log("JSON Parse failed!" + e);
                return;
              }
              if (data.result) {
                alert('Result is OK!');
                Y.log('Success');

                //remove existing div nodes
                var nodes = Y.one('#helpdesk');
                nodes.all('div').remove();
                //create and append new nodes
                var new_node = Y.one('#helpdesk_success');
                new_node.show();
              }
            },
            failure: function (o, response) {
              alert('Not OK!');
              Y.log('Failure' + response.responseText);
              //create and append new nodes
              var new_node = Y.one('#helpdesk_failure');
               new_node.show();
            }
         }
    };

    Y.io(M.cfg.wwwroot + '/blocks/helpdesk/sendmail.php', ioconfig);
}