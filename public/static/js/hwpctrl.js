var MinVersion = 0x05050111;
var vHwpCtrl;
var HWP_UNIT	= 283.465;
function initHwpCtrl() {
 	vHwpCtrl = document.getElementById("HwpCtrl");

	if(!_VerifyVersion()) {
		return false;
	}

	InitToolBarJS();
	return true;
}
 
function _VerifyVersion() {
	//설치 확인
	if(typeof vHwpCtrl.open === "undefined") {
		alert("한글 컨트롤이 설치되지 않았습니다.");
		return false;
	}

	//버젼 확인
	CurVersion = vHwpCtrl.Version;
	if(CurVersion < MinVersion) {
		alert("HwpCtrl의 버젼이 낮아서 정상적으로 동작하지 않을 수 있습니다.\n"+
			"최신 버젼으로 업데이트하기를 권장합니다.\n\n"+
			"현재 버젼:" + CurVersion + "\n"+
			"권장 버젼:" + MinVersion + " 이상"			
			);
		return false;
	}
	return true;
}
 
function InitToolBarJS()	// 툴바 보여주기
{

	vHwpCtrl.ReplaceAction("FileNew", "HwpCtrlFileNew");
	vHwpCtrl.ReplaceAction("FileSave", "HwpCtrlFileSave");
	vHwpCtrl.ReplaceAction("FileSaveAs", "HwpCtrlFileSaveAs");
	vHwpCtrl.ReplaceAction("FileOpen", "HwpCtrlFileOpen");
 
	vHwpCtrl.SetToolBar(3, "FileNew, FileSave, FileSaveAs, FileOpen");
	vHwpCtrl.SetToolBar(3, 1);
	vHwpCtrl.ShowToolBar(true);
}
