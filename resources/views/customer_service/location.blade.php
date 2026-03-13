@extends('layouts.header')

@section('title', '찾아오시는길 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <div class="st-left">
      <h4>찾아오시는길</h4>  
      <div class="category">
        <button type="button" class="btn3 branch-btn" id="cateBtn">본점<img src="https://xn--hz2bqq88l.com/images/icon/down.svg"></button>
        <ul id="cateList" class="brnach-list">
          <li><a href="#" data-tab="branch1">본점</a></li>
          <li><a href="#" data-tab="branch2">원주지점</a></li>
          <li><a href="#" data-tab="branch3">대전지점</a></li>
          <li><a href="#" data-tab="branch4">대구지점</a></li>
          <li><a href="#" data-tab="branch5">광주지점</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 1. 지도 노드 -->
  <div id="branch1" class="map-wrap show">
    <!-- 본점 -->
    <div id="daumRoughmapContainer1769064687450" class="root_daum_roughmap root_daum_roughmap_landing"></div>
  </div>
  <div id="branch2" class="map-wrap">
    <!-- 원주 -->
    <div id="daumRoughmapContainer1769218430704" class="root_daum_roughmap root_daum_roughmap_landing"></div>
  </div>
  <div id="branch3" class="map-wrap">
    <!-- 대전 -->
    <div id="daumRoughmapContainer1769218470424" class="root_daum_roughmap root_daum_roughmap_landing"></div>
  </div>
  <div id="branch4" class="map-wrap">
    <!-- 대구 -->
    <div id="daumRoughmapContainer1769218494221" class="root_daum_roughmap root_daum_roughmap_landing"></div>
  </div>
  <div id="branch5" class="map-wrap">
    <!-- 광주 -->
    <div id="daumRoughmapContainer1769218555055" class="root_daum_roughmap root_daum_roughmap_landing"></div>
  </div>

</div>


<!-- 2. 설치 스크립트 -->
<script charset="UTF-8" class="daum_roughmap_loader_script" src="https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js"></script>

<!-- 3. 실행 스크립트 -->
<!-- 본점 -->
<script charset="UTF-8">
  new daum.roughmap.Lander({
    "timestamp" : "1769064687450",
    "key" : "g38focg6ry9",
  }).render();
</script>
<!-- 원주 -->
<script charset="UTF-8">
	new daum.roughmap.Lander({
		"timestamp" : "1769218430704",
		"key" : "gjz4ozgjmfv",
	}).render();
</script>
<!-- 대전 -->
<script charset="UTF-8">
	new daum.roughmap.Lander({
		"timestamp" : "1769218470424",
		"key" : "gj35xic6wfa",
	}).render();
</script>
<!-- 대구 -->
<script charset="UTF-8">
	new daum.roughmap.Lander({
		"timestamp" : "1769218494221",
		"key" : "gjz667fmg2t",
	}).render();
</script>
<!-- 광주 -->
<script charset="UTF-8">
	new daum.roughmap.Lander({
		"timestamp" : "1769218555055",
		"key" : "gjz7katexbw",
	}).render();
</script>

<style>
  .root_daum_roughmap{width: 100%;}
  .root_daum_roughmap .wrap_map{height: 500px;}
  .root_daum_roughmap .wrap_btn_zoom{z-index: 2;}
</style>

@endsection