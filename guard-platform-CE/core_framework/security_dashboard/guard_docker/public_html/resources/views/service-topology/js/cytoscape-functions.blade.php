<script type="text/javascript">
    /**
     * @param env
     * @returns {Array}
     */
    function createNodes(){
        let nodes = [];
        let networkNodes = ['mobile-phone', 'user-vm', 'digit-cyber', 'vision-tech'];
        $.each(env, function(index, value){
            let nodeClass = "";
            if(value['enabled'] === false){
                nodeClass = "disabled"
            }

            if(networkNodes.includes(value['id'])){
                if(value['id'] === 'digit-cyber' || value['id'] === 'vision-tech'){
                    nodes.push(
                        {
                            group: "nodes",
                            // data: {id: value['id'], name: value['description'], parent: 'digit-vision-network'},
                            data: {id: value['id'], name: value['id']},
                            classes: 'l1 ' + nodeClass,
                            position: (value['position'] != "undefined") ? value['position'] : ""
                        }
                    )
                }else{
                    nodes.push(
                        {
                            group: "nodes",
                            // data: {id: value['id'], name: value['description'], parent: 'mobile-vm-network'},
                            data: {id: value['id'], name: value['id']},
                            classes: 'l1 ' + nodeClass,
                            position: (value['position'] != "undefined") ? value['position'] : ""
                        }
                    )
                }
            }else{
                nodes.push(
                    {
                        group: "nodes",
                        data: {id: value['id'], name: value['id']},
                        classes: 'l1 ' + nodeClass,
                        position: (value['position'] != "undefined") ? value['position'] : ""
                    }
                )
            }
        });
        return nodes;
    }

    /**
     *
     * @returns {Array}
     */
    function createConnections(){
        let connections = [];
        $.each(con, function(index, value) {
            // { data: { id: 's1-ds', source: 'service1', target: 'datasink' } },
            connections.push(
                {
                data:
                    {
                        id: value['node1'] + '-' + value['node2'],
                        source: value['node1'],
                        target: value['node2'],
                        type: value['connection_type']
                    }
                }
            )
        });
        return connections;
    }

    /**
     *
     * @param cy
     * @param env
     * current types: applications, vm, cloud, gateway, mobile
     */
    function addIconsToStyle(cy){
        $.each(env, function (index, value) {
            let imagePath = '{!! asset('images/service-topology-icons/:id.png') !!}';
            imagePath = imagePath.replace(':id', value['type_id']);
            cy.style()
                .selector('[id = "'+value["id"]+'"]')
                // .selector('#'+value['id'])
                .style({
                    'background-image': imagePath,
                    'shape': 'round-rectangle'
                })
                .update();
        });
    }


    /**
     * return the Name (description) of Service
     * @param id
     */
    function getConnectionName(id){
        let name;
        $.each(env, function(index, value){
            if(value['id'] === id){
                name = value['description'];
            }
        });
        return name;
    }

    function getNetworkDescription(type){
        let description;

        $.each(net, function(index, value){
           if(value['type_id'] === type){
               description = value['type_description'];
           }
        });
        return description;
    }

    function getNodeType(id){
        let type;
        $.each(env, function(index, value){
            if(value['id'] === id){
                type = value['type_id'];
            }
        });
        return type;
    }

    function removeNodesEdgesClasses(cy){
        cy.edges().removeClass('selectedEdge');
        cy.nodes().removeClass('edgeSelectedNode');
        cy.nodes().removeClass('inactive');
        cy.edges().removeClass('nodeSelectedEdge');
    }


    function prepareDeleteChainBtn(id, env, rootEnvs){

        let chain = false;
        let targetEnv = getExecEnvironment(id, env);
        let deleteBtn = $('#deleteChain');
        let chainIcon = $('#iconChain');

        $.each(rootEnvs, function (key, value) {
            if(value.hostname === targetEnv.hostname){
                chain = true;
            }
        });

        if(chain){
            if(deleteBtn.hasClass('d-none')){
                deleteBtn.removeClass('d-none');
            }

            if(chainIcon.hasClass('d-none')){
                chainIcon.removeClass('d-none');
            }

            if(typeof targetEnv.hostname !== "undefined"){

                let url = "{!! route('service-topology.chain.delete', ':hostname') !!}";
                url = url.replace(':hostname', targetEnv.hostname);
                deleteBtn.closest('.delete-method').attr('action', url);
                deleteBtn.attr('data-original-title', 'Delete Service Chain: '+targetEnv.hostname);
                chainIcon.attr('data-original-title', 'Service Chain: '+targetEnv.hostname);
            }
        }else{
            if(!deleteBtn.hasClass('d-none')){
                deleteBtn.addClass('d-none');
            }

            if(!chainIcon.hasClass('d-none')){
                chainIcon.addClass('d-none');
            }
        }

    }

    function getExecEnvironment(id, envs){
        return envs.find(x => x.id === id);
    }

</script>