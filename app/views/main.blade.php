@extends('layouts.master')

@section('content')
            
<div class="row-fluid">
    <div class="panel panel-default span12">
        <div class="panel-heading well">
            <h3> 공지사항</h3>
        </div>
        <div class="panel-body">
            <div class="row-fluid">
                <div class="span12">
                    <h4>사이트맵</h4>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                @foreach($menus as $menu)
                                <th><a href="{{$menu->href}}">{{$menu->name}}</a></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($menus as $menu)
                                <td>
                                <ul>
                                    @foreach ($menu->children as $c)
                                    <li><a href="{{$c->href}}">{{$c->name}}</a></li>    
                                    @endforeach
                                </ul>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


             <div class="row-fluid">
                 <div class="span12">
                     <h4>자주묻는질문</h4>
                 </div>
             </div>

            <div class="row-fluid">
                <div class="span12">
                    <div class="accordion" id="accordion">
                         <div class="accordion-group">
                             <div class="accordion-heading">
                                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                                     [경비속보] 'HwpCtrl이 설치되지 않았습니다.' 오류가 뜰 때
                                 </a>
                             </div>
                             <div id="collapseOne" class="accordion-body collapse">
                                 <div class="accordion-inner">
                                     <p>경비속보 작성기를 사용하시려면 한글 2005 버전 이상이 설치되어 있어야 합니다.</p>
                                     <p>만약 설치가 되어 있음에도 같은 오류가 발생한다면 다음 파일을 내려받아 실행해주세요.</p>
                                     <p> <a href="{{url('static/misc/hwreg.bat')}}">레지스트리 패치</a> </p>
                                 </div>
                             </div>
                         </div>
                         <div class="accordion-group">
                             <div class="accordion-heading">
                                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                                     [매뉴얼] 사용자 매뉴얼 (경비속보, 경비예산)
                                 </a>
                             </div>
                             <div id="collapseTwo" class="accordion-body collapse">
                                 <div class="accordion-inner">
                                     <a href="{{url('static/misc/manual/user.zip')}}">사용자 매뉴얼</a>
                                 </div>
                             </div>
                         </div>
                         <div class="accordion-group">
                             <div class="accordion-heading">
                                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
                                     [매뉴얼] 지방청관리자 매뉴얼
                                 </a>
                             </div>
                             <div id="collapseThree" class="accordion-body collapse">
                                 <div class="accordion-inner">
                                     <a href="{{url('static/misc/manual/admin.zip')}}">지방청관리자 매뉴얼</a>
                                 </div>
                             </div>
                         </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@stop