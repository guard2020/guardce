<script type="text/javascript">
    $(function(){

        let nodes = createNodes();
        let connections = createConnections();

        // create Cy instance
        let cyInstance = cytoscape({
            container: $('#chart'),
            layout: {
                //name: 'preset'
                name: 'cose'
            },
            // minZoom: 0.5,
            maxZoom: 3,
            zoomingEnabled: true,
            userZoomingEnabled: true,
            boxSelectionEnabled: false,
            autounselectify: true,
            wheelSensitivity: 0.3,

            style: cytoscape.stylesheet()
                .selector('node')
                .css({
                    'height': 50,
                    'width': 50,
                    // 'background': '100px 100px',
                    'background-fit': 'contain',
                    'background-repeat': 'no-repeat',
                    'border-color': '#12439B',
                    'border-width': 2,
                    'background-color': '#d6f4f8',
                })
                .selector('edge')
                .css({
                    'curve-style': 'bezier',
                    'width': 2,
                    'target-arrow-shape': 'triangle',
                    'line-color': '#202020',
                    'target-arrow-color': '#202020',
                    'opacity': 0.5
                })
                .selector('.inactive')
                .css({
                    'background-color': '#00BCD4',
                    'border-color': '#12439B',
                })
                .selector('.wifi')
                .css({
                    'line-style': 'dashed'
                })
                .selector('.p2p')
                .css({
                    'width': 3,
                    'curve-style': 'straight',
                    'opacity': 0.8,
                    'line-color': 'black',
                    'target-arrow-color': 'black',

                })
                .selector('.network-slice')
                .css({
                    'target-arrow-shape': 'none',
                    'line-style': 'solid',
                    'line-color': '#FFB400',
                    'target-arrow-color': '#FFB400',

                })
                .selector('.disabled')
                .css({
                    'opacity': 0.2,
                })
                .selector('.nodeSelectedEdge')
                .css({
                    'line-color': '#12439B',
                    'target-arrow-color': '#12439B',
                    'opacity': 1
                })
                .selector('.selectedEdge')
                .css({
                    'line-color': '#00BCD4',
                    'target-arrow-color': '#00BCD4',
                    'opacity': 1,
                    'width': 4
                })
                .selector('.edgeSelectedNode')
                .css({
                    'border-color': '#12439B',
                })
                .selector('.network')
                .css({
                    'border-radius': 2,
                    'border': 'transparent',
                }),

            elements: {
                nodes: nodes,
                edges: connections,
            }
        });

        addIconsToStyle(cyInstance);

        $.each(cyInstance.edges(), function(index, value){
            if(this.data('type') === 'wifi'){
                this.addClass('wifi');
            }else if(this.data('type') === 'pnt2pnt'){
                this.addClass('p2p');
            } else if(this.data('type') === 'network-slice') {
                this.addClass('network-slice');
            }
        });

        cyInstance.nodeHtmlLabel([
            {
                query: '.l1',
                halign: 'center',
                valign: 'bottom',
                halignBox: 'center',
                valignBox: 'bottom',
                tpl: function(data) {
                    let nodeLabel = typeof data.name === 'undefined' ? 'No name' : (data.name.length <= 20 ? data.name : data.name.substring(0, data.name.indexOf("", 10))+'...');
                    return '<p class="cy-title__p1"><a style="text-decoration:none; color: black; size: A3;" href="#" title="'+data.name+'">'+nodeLabel+'</a></p>';
                }
            },
        ]);

        cyInstance.on('click', 'node', function () {
            let service;
            let id = this.id();

            prepareDeleteChainBtn(id, env, rootEnvs);

            removeNodesEdgesClasses(cyInstance);

            this.addClass('inactive');
            // $.each(cyInstance.$('#'+this.id()).connectedEdges(), function (index, value) {
            $.each(cyInstance.$('[id = "'+this.id()+'"]').connectedEdges(), function (index, value) {
                this.addClass('nodeSelectedEdge');
            });

            $.each(env, function(index, value){
                if(value['id'] === id){
                    service = value;
                }
            });

            let enabled = service['enabled']===true?'Enabled':'Disabled';
            let badge = service['enabled']===true?'badge bg-success ml-2':'badge bg-danger ml-2';
            let serviceCard = $('.service-info');
            let cardHeight = $('#chart').height();
            let imagePath = '{!! asset('images/service-topology-icons/:id.png') !!}';
            serviceCard.empty();
            imagePath = imagePath.replace(':id', service['type_id']);
            let hostname = service['hostname'].length <= 50 ? service['hostname'] : service['hostname'].substring(0, service['hostname'].indexOf("", 50));

            serviceCard.append(
                '<h4 class=" font-weight-semibold mb-3"><img class="border-2 mr-3" style="padding: 0.3rem; border-radius: 10%;" src="'+imagePath+'" height="50" width="60">'+service['id']+'</h4>' +
                '<li class="odd list-group-item"><span class="font-weight-bold">Hostname:</span><div class="ml-auto">'+hostname+'</div></li>'
                // '<li class="odd list-group-item"><span class="font-weight-bold">Hostname:</span><div class="ml-auto">'+service['hostname']+'</div></li>'
            );

            if(typeof service['stage'] != 'undefined'){
                serviceCard.append(
                    '<li class="even list-group-item"><span class="font-weight-bold">Stage:</span><div class="ml-auto">'+service['stage'].capitalize()+'</div></li>'
                );
            }

            if(typeof enabled != 'undefined'){
                serviceCard.append(
                    '<li class="odd list-group-item"><span class="font-weight-bold">Status:</span><div class="ml-auto"><span class="'+badge+'">'+enabled+'</span></div></li>'
                );
            }

            if(typeof service['description'] != 'undefined'){
                serviceCard.append(
                    '<li class="even list-group-item"><span class="font-weight-bold">Description:</span><div class="ml-auto">'+service['description']+'</div></li>'
                );
            }

            if(typeof service['type_description'] != 'undefined'){
                serviceCard.append(
                    '<li class="odd list-group-item"><span class="font-weight-bold">Service Type:</span><div class="ml-auto">'+service['type_description']['name']+'</div></li>'
                );
            }


            if(typeof service['partner'] != 'undefined'){
                serviceCard.append(
                    '<li class="even list-group-item"><span class="font-weight-bold">Partner:</span><div class="ml-auto">'+service['partner'].capitalize()+'</div></li>'
                );
            }

            if(typeof service['lcp'] != 'undefined'){
                serviceCard.append(
                    '<li class="list-group-item"><span class="font-weight-bold">LCP:</span></li>'+
                    '<li class="ml-3 list-group-item"><span class="font-weight-bold1">Port:</span><div class="ml-auto">'+service['lcp']['port']+'</div></li>'
                );

                if(typeof service['lcp']['last_heartbeat'] != 'undefined') {
                    serviceCard.append(
                        '<li class="ml-3 list-group-item"><span class="font-weight-bold1">Service Started:</span><div class="ml-auto">'+moment(service['lcp']['started']).format('D MMM YYYY - H:mm:ss')+'</div></li>'+
                        '<li class="ml-3 list-group-item"><span class="font-weight-bold1">Last Heartbeat:</span><div class="ml-auto">'+moment(service['lcp']['last_heartbeat']).format('D MMM YYYY - H:mm:ss')+'</div></li>'

                    );
                }
            }

            let connCount = 0;
            $.each(connections, function (index, value) {
                let connection = value.data;
                if(connection['source'] === service['id']){
                    if(connCount === 0){
                        serviceCard.append('<li class="list-group-item"><span class="font-weight-bold">Connections:</span></li>');
                    }
                    connCount++;
                    serviceCard.append('' +
                        '<li class="ml-3 list-group-item"><span class="font-weight-sillybold">'+connCount+'. '+connection['type'].capitalize()+' connection to '+getConnectionName(connection['target'])+'</span></li>'
                    );

                }else if(connection['target'] !== "" && connection['target'] === service['id']){
                    if(connCount === 0){
                        serviceCard.append('<li class="list-group-item"><span class="font-weight-bold">Connections:</span></li>');
                    }
                    connCount++;
                    serviceCard.append('' +
                        '<li class="ml-3 list-group-item"><span class="font-weight-sillybold">'+connCount+'. '+connection['type'].capitalize()+' connection from '+getConnectionName(connection['source'])+'</span></li>'
                    );
                }
            })
        });

        cyInstance.on('click', 'edge', function () {
            let id = this.id();
            let connection;

            removeNodesEdgesClasses(cyInstance);

            let deleteBtn = $('#deleteChain');
            if(!deleteBtn.hasClass('d-none')){
                $('#deleteChain').addClass('d-none');
            }

            this.addClass('selectedEdge');
            // $.each(cyInstance.$('#'+this.id()).connectedNodes(), function (index, value) {
            $.each(cyInstance.$('[id = "'+this.id()+'"]').connectedNodes(), function (index, value) {
                this.addClass('edgeSelectedNode');
            });

            $.each(con, function(index, value){
                if(value['node1']+'-'+value['node2'] === id){
                    connection = value;
                }
            });

            let serviceCard = $('.service-info');
            let typeDescription = getNetworkDescription(connection['connection_type']);
            let sourceImagePath = '{!! asset('images/service-topology-icons/:id.png') !!}';
            let targetImagePath = '{!! asset('images/service-topology-icons/:id.png') !!}';
            let sourceType =getNodeType(connection['node1']);
            let targetType = getNodeType(connection['node2']);

            serviceCard.empty();
            // serviceCard.height(cardHeight);--TODO-Change height? What looks better?
            sourceImagePath = sourceImagePath.replace(':id', sourceType);
            targetImagePath = targetImagePath.replace(':id', targetType);
            //add info box, --TODO-can be extended with fields
            serviceCard.append(
                '<h4 class=" font-weight-semibold mb-3">' +
                '<img class="border-2 mr-3" style="padding: 0.3rem; border-radius: 10%;" src="'+sourceImagePath+'" height="50" width="60">' +
                '<i class="fas fa-3x fa-long-arrow-alt-right text-guard align-middle"></i>'+
                '<img class="border-2 ml-3 mr-3" style="padding: 0.3rem; border-radius: 10%;" src="'+targetImagePath+'" height="50" width="60">' +
                'Connection</h4>' +
                '<li class="list-group-item"><span class="font-weight-bold">Connection Type:</span><div class="ml-auto">'+typeDescription['name']+'</div></li>'+
                '<li class="list-group-item"><span class="font-weight-bold">Source Service:</span><div class="ml-auto">'+getConnectionName(connection['node1'])+'</div></li>'+
                '<li class="list-group-item"><span class="font-weight-bold">Target Service:</span><div class="ml-auto">'+getConnectionName(connection['node2'])+'</div></li>'+
                '<li class="list-group-item"><span class="font-weight-bold">Description:</span><div class="ml-auto">'+typeDescription['description']+'</div></li>'
            );
        });

        $("#deleteChain").click(function () {
            let r = confirm('{!! __('Are you sure you want to delete this chain and the related pipelines?') !!}');
            return (r === true);
        });

    });
</script>