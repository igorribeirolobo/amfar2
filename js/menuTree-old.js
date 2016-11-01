var fcl1Ovr="#989C85";	// ocre
var fcl2Ovr="#989C85";	// ocre
var bgColor="transparent";
var bgColorSm="#efefef";
var lColor="#606060";
var shadow="#404040";
var border="transparent";
var separator="#ffffff";
var timeout=100;
var opacity=100;
var imgTri="css/tri.png";
var imgTriDown="css/tridown.png";
var imgTriDownWhite="css/tridown_white.png";

// menu Home
stm_bm(["menu2c10",430,"","",0,"","",0,0,250,0,timeout,1,0,0,"","",0],this);
stm_bp("p0",[0, 4, 0, 0, 1, 2, 0, 7,opacity,"",-2,"",-2, 90, 0, 0, shadow, border,"", 3, 0, 0, lColor]);

stm_ai("p0i0",[0,"  Homepage","","",-1,-1,0,"index.php?lnk=10","_self","","","","",0,0,0,"","",0,0,0,0,1,bgColor,0,bgColor,0,"","",3,3,0,0,lColor,lColor,lColor,fcl1Ovr,"bold 8pt Arial","bold 8pt Arial",0,0]);
stm_bpx("p1","p0",[1,4,0,0,1,2,9,0, opacity,"", 5,"", 4, 90, 2, 5]);	// cria a sombra sobre os subitens

stm_aix("p1i1","p0i0",[0,"Homepage","","",-1,-1,0,"index.php?lnk=10","","","","",imgTri,5,10,0,"","",0,0,0,0,1,bgColorSm,0,bgColorSm,0,"","",3,3,0,0,"#c0c0c0 #666666 #666666 #c0c0c0","#c0c0c0 #666666 #666666 #c0c0c0",lColor,fcl2Ovr]);
stm_aix("p1i1","p1i1",[0,"Conheça-nos","","",-1,-1,0,"index.php?lnk=11"]);
stm_aix("p1i1","p1i1",[0,"Diretoria","","",-1,-1,0,"index.php?lnk=12"]);
stm_aix("p1i1","p1i1",[0,"Agenda","","",-1,-1,0,"index.php?lnk=13"]);
stm_aix("p1i1","p1i1",[0,"Estatuto","","",-1,-1,0,"index.php?lnk=14"]);
stm_aix("p1i1","p1i1",[0,"Nossa Localização    ","","",-1,-1,0,"index.php?lnk=15"]);
stm_aix("p1i1","p1i1",[0,"Galeria de Fotos","","",-1,-1,0,"index.php?lnk=17"]);
stm_ep();

stm_aix("p0i9","p0i0",[0," Notícias","","",-1,-1,0,"index.php?lnk=30"]);
stm_ep();

// menu Agenciamento
stm_aix("p0i2","p0i0",[0,"  Agenciamento","","",-1,-1,0,""]);
//stm_bpx("p4","p1",[]);
//stm_aix("p1i1","p0i0",[0,"Candidatos","","",-1,-1,0,"index.php?lnk=40&flag=1","","","","",imgTri,5,10,0,"","",0,0,0,0,1,bgColorSm,0,bgColorSm,0,"","",3,3,0,0,"#c0c0c0 #666666 #666666 #c0c0c0","#c0c0c0 #666666 #666666 #c0c0c0",lColor,fcl2Ovr]);
//stm_aix("p42","p1i1",[0,"Empresas      ","","",-1,-1,0,"index.php?lnk=40&flag=2"]);
stm_ep();



// menu eventos
stm_aix("p0i4","p0i2",[0," Eventos  ","","",-1,-1,0,"index.php?lnk=20&st=0"]);
//stm_bpx("p4","p1",[]);
//stm_aix("p4i1","p0i0",[0,"Eventos AMF","","",-1,-1,0,"index.php?lnk=20&st=0","","","","",imgTri,5,10,0,"","",0,0,0,0,1,bgColorSm,0,bgColorSm,0,"","",3,3,0,0,"#c0c0c0 #666666 #666666 #c0c0c0","#c0c0c0 #666666 #666666 #c0c0c0",lColor,fcl2Ovr]);
stm_ep();


// menu Cursos
stm_aix("p0i6","p0i2",[0," Cursos  ","","",-1,-1,0,""]);
stm_bpx("p6","p1",[]);
stm_aix("p6i1","p1i1",[0,"Cursos Prog. AMF - Atualização","","",-1,-1,0,"index.php?lnk=50&st=0"]);
stm_aix("p6i1","p1i1",[0,"Cursos Prog. AMF - Especialização   ","","",-1,-1,0,"index.php?lnk=50&st=1"]);
//stm_aix("p6i2","p1i1",[0,"Cursos Diversos","","",-1,-1,0,"index.php?lnk=50&st=2"]);
stm_ep();


// menu Forum
stm_aix("p0i14","p0i2",[0," Fórum Técnico","","",-1,-1,0,""]);
stm_bpx("p6","p1",[]);
stm_aix("p61","p1i1",[0,"Fórum Técnico    ","","",-1,-1,0,"index.php?lnk=60"]);
stm_aix("p62","p1i1",[0,"Livro de Visitas","","",-1,-1,0,"index.php?lnk=61"]);
stm_ep();


// menu Links
stm_aix("p0i10","p0i2",[0," Links Úteis ","","",-1,-1,0,"index.php?lnk=70"]);
stm_bpx("p10","p1",[]);
stm_aix("p101","p1i1",[0,"Links Área de Saúde    ","","",-1,-1,0,"index.php?lnk=71"]);
stm_aix("p102","p1i1",[0,"Legislação","","",-1,-1,0,"index.php?lnk=72"]);
//stm_aix("p102","p1i1",[0,"Guia Genéricos","","",-1,-1,0,"index.php?lnk=73"]);
stm_ep();


// menu Login
stm_aix("p0i12","p0i2",[0," Login ","","",-1,-1,0,"index.php?lnk=80"]);
stm_bpx("p12","p1",[]);
stm_aix("p12i0","p1i1",[0,"Usuário Cadastrado    ","","",-1,-1,0,"index.php?lnk=80&flag=1"]);
stm_aix("p12i1","p1i1",[0,"Associe-se a AMF","","",-1,-1,0,"index.php?lnk=80&flag=2"]);
stm_aix("p12i1","p1i1",[0,"Downloads","","",-1,-1,0,"index.php?lnk=83"]);
stm_ep();

// menu Login
stm_aix("p0i12","p0i2",[0," Fale Conosco ","","",-1,-1,0,""]);
stm_bpx("p12","p1",[]);
stm_aix("p12i0","p1i1",[0,"Contato","","",-1,-1,0,"index.php?lnk=85"]);
stm_aix("p12i1","p1i1",[0,"Incluir no Mailing    ","","",-1,-1,0,"index.php?lnk=86"]);
stm_ep();

stm_aix("p0i14","p0i2",[0," Indique este Site ","","",-1,-1,0,"index.php?lnk=87","","","","","",0,0,0,"","",0,0]);
stm_ep();


// Acesso Restrito
stm_aix("p0i12","p0i2",[0," Restrito","","",-1,-1,0,"index.php?lnk=90"]);
stm_ep();

stm_em();
