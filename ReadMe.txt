项目结束后，把闭包和jquery对比看下tab切换案例


admin:后台管理系统界面
static:静态文件：后台原封不动返回给客服端
static


JS流行规范:Airbnb JavaScript

批处理后缀名 ren *.html *.php     --html-批量改php
sublime  Ctrl+Shift+f   批量全局修改代码段内容


categories.php------
1.sidebar公共页面的抽离：在相应模块设置$current_index


**************************************************************************login.php
校验时数据库密码的加密
设置session，在每个页面时判断是否已登录
补漏
animate.css--登录窗口抖动
正则表达式+jquery---判断邮箱
ajax请求数据接口
jquery动画应用

*********************************************************************index.php
获取数据库展示在页面中
chart.js库-英文库
echarts库--中文  ----canvas数据效果展示


*******************************************************************categories.php
1.先处理查询展示功能
2.添加分类,添加数据库后展示到页面中
判断是否为POST请求，且url中没有id (主要添加失败和成功提示)

3.删除公共，通过url?id=传递对于删除到一个delete.php文件去操作
 a..批量删除,
  客服端js操作,给每个input添加一个H5的data=id(自己对应得id).
  先定义一个数组var arr = []; 
  通过jquery去操作，$('input').on('chuange',function(){
  	var id = $(this).data(id);
  	$(this).prop('checked'){
  	数组中push
  	}else{
  	arr.splice(arr.indexof(id),1);
  	}

  	arr.length ? BLOCK:NONE;

  })
dom元素中的补漏--search  


4.编辑功能，通过id获取编辑的数据，通过数据的判断为添加还是编辑功能去
做相应的操作


--posts页面----服务端的分页功能
筛选


----comments----分页展示数据-
筛选 --分页控件

