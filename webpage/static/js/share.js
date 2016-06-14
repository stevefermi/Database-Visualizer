function getAjax(_method,_url,_data,callback){
        $.ajax({
            url:_url,
            type:_method,
            data:_data,
            async:false,
            success:callback,
            error:function(xhr,ajaxOptions,thrownError){
                console.log(xhr);
            }
        });
}
function render(data)
{
    console.log(data.result);
    var template = $('#template').html();
    Mustache.parse(template);   // optional, speeds up future uses
      var rendered = Mustache.render(template, {result: data.result});
    $('#posts_target').html(rendered);
}

function postdraw(did,publisherid,tablename,chart,settings,charttype){
    var data={"did":did,"userid":publisherid,"tablename":tablename};
    var queryDataUrl='http://159.203.251.186/dbhost/querydata';
    var settings=JSON.parse(settings);
    console.log(settings);
    var graphData=[];
    getAjax("POST",queryDataUrl,data,function(msg){
        var result = msg.result;
        if (charttype=='line'){
            x_axis=settings.x;
            y_axis=settings.y;
            $.each(result,function(index,data){
                graphData.push(JSON.parse('{"'+x_axis+'":"'+data[x_axis]+'","'+y_axis+'":"'+data[y_axis]+'"}'));
            });
        console.log(graphData);
        chart.source(graphData);
        chart.line().position(x_axis+'*'+y_axis).color(x_axis);
        chart.render();    
    }
    });
}
function queryPosts()
{
    var url='http://159.203.251.186/post';
    var method='GET';
    getAjax(method,url,null,function(msg){
        render(msg);
        for(index in msg.result)
        {
            each=msg.result[index];
            console.log(each);
            console.log('graph_'+each.id);
            var chart = new G2.Chart({
                    id:'graph_'+each.id,
                    width:800,
                    height:400
                });
            console.log(each.did);
            postdraw(each.did,each.publisherid,each.tablename,chart,each.settings,each.chart_type);
            console.log('draw Finished');    
    }
    });
} 

