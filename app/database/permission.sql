BEGIN TRANSACTION;

CREATE TABLE system_group (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100));

CREATE TABLE system_program (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100),
    controller varchar(100));

CREATE TABLE system_unit (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100),
    connection_name varchar(100));

CREATE TABLE system_preference (
    id text,
    value text
);

CREATE TABLE system_user (
    id INTEGER PRIMARY KEY NOT NULL,
    name varchar(100),
    login varchar(100),
    password varchar(100),
    email varchar(100),
    frontpage_id int,
    system_unit_id int references system_unit(id),
    active char(1),
    accepted_term_policy char(1), accepted_term_policy_at TEXT,
    FOREIGN KEY(frontpage_id) REFERENCES system_program(id));

CREATE TABLE system_user_unit (
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id int,
    system_unit_id int,
    FOREIGN KEY(system_user_id) REFERENCES system_user(id),
    FOREIGN KEY(system_unit_id) REFERENCES system_unit(id));

CREATE TABLE system_user_group (
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id int,
    system_group_id int,
    FOREIGN KEY(system_user_id) REFERENCES system_user(id),
    FOREIGN KEY(system_group_id) REFERENCES system_group(id));

CREATE TABLE system_group_program (
    id INTEGER PRIMARY KEY NOT NULL,
    system_group_id int,
    system_program_id int,
    FOREIGN KEY(system_group_id) REFERENCES system_group(id),
    FOREIGN KEY(system_program_id) REFERENCES system_program(id));
    
CREATE TABLE system_user_program (
    id INTEGER PRIMARY KEY NOT NULL,
    system_user_id int,
    system_program_id int,
    FOREIGN KEY(system_user_id) REFERENCES system_user(id),
    FOREIGN KEY(system_program_id) REFERENCES system_program(id));
    
INSERT INTO system_group VALUES(1,'Admin');
INSERT INTO system_group VALUES(3,'Pessoas');
INSERT INTO system_group VALUES(4,'Serviços');
INSERT INTO system_group VALUES(5,'Contratos');
INSERT INTO system_group VALUES(6,'Faturas');
INSERT INTO system_group VALUES(7,'Financeiro');
INSERT INTO system_group VALUES(8,'Ferramentas');

INSERT INTO system_program VALUES(1,'System Group Form','SystemGroupForm');
INSERT INTO system_program VALUES(2,'System Group List','SystemGroupList');
INSERT INTO system_program VALUES(3,'System Program Form','SystemProgramForm');
INSERT INTO system_program VALUES(4,'System Program List','SystemProgramList');
INSERT INTO system_program VALUES(5,'System User Form','SystemUserForm');
INSERT INTO system_program VALUES(6,'System User List','SystemUserList');
INSERT INTO system_program VALUES(7,'Common Page','CommonPage');
INSERT INTO system_program VALUES(8,'System PHP Info','SystemPHPInfoView');
INSERT INTO system_program VALUES(9,'System ChangeLog View','SystemChangeLogView');
INSERT INTO system_program VALUES(10,'Welcome View','WelcomeView');
INSERT INTO system_program VALUES(11,'System Sql Log','SystemSqlLogList');
INSERT INTO system_program VALUES(12,'System Profile View','SystemProfileView');
INSERT INTO system_program VALUES(13,'System Profile Form','SystemProfileForm');
INSERT INTO system_program VALUES(14,'System SQL Panel','SystemSQLPanel');
INSERT INTO system_program VALUES(15,'System Access Log','SystemAccessLogList');
INSERT INTO system_program VALUES(16,'System Message Form','SystemMessageForm');
INSERT INTO system_program VALUES(17,'System Message List','SystemMessageList');
INSERT INTO system_program VALUES(18,'System Message Form View','SystemMessageFormView');
INSERT INTO system_program VALUES(19,'System Notification List','SystemNotificationList');
INSERT INTO system_program VALUES(20,'System Notification Form View','SystemNotificationFormView');
INSERT INTO system_program VALUES(21,'System Document Category List','SystemDocumentCategoryFormList');
INSERT INTO system_program VALUES(22,'System Document Form','SystemDocumentForm');
INSERT INTO system_program VALUES(23,'System Document Upload Form','SystemDocumentUploadForm');
INSERT INTO system_program VALUES(24,'System Document List','SystemDocumentList');
INSERT INTO system_program VALUES(25,'System Shared Document List','SystemSharedDocumentList');
INSERT INTO system_program VALUES(26,'System Unit Form','SystemUnitForm');
INSERT INTO system_program VALUES(27,'System Unit List','SystemUnitList');
INSERT INTO system_program VALUES(28,'System Access stats','SystemAccessLogStats');
INSERT INTO system_program VALUES(29,'System Preference form','SystemPreferenceForm');
INSERT INTO system_program VALUES(30,'System Support form','SystemSupportForm');
INSERT INTO system_program VALUES(31,'System PHP Error','SystemPHPErrorLogView');
INSERT INTO system_program VALUES(32,'System Database Browser','SystemDatabaseExplorer');
INSERT INTO system_program VALUES(33,'System Table List','SystemTableList');
INSERT INTO system_program VALUES(34,'System Data Browser','SystemDataBrowser');
INSERT INTO system_program VALUES(35,'System Menu Editor','SystemMenuEditor');
INSERT INTO system_program VALUES(36,'System Request Log','SystemRequestLogList');
INSERT INTO system_program VALUES(37,'System Request Log View','SystemRequestLogView');
INSERT INTO system_program VALUES(38,'System Administration Dashboard','SystemAdministrationDashboard');
INSERT INTO system_program VALUES(39,'System Log Dashboard','SystemLogDashboard');
INSERT INTO system_program VALUES(40,'System Session dump','SystemSessionDumpView');
INSERT INTO system_program VALUES(41,'Estado Form','EstadoForm');
INSERT INTO system_program VALUES(42,'Estado List','EstadoList');
INSERT INTO system_program VALUES(43,'Cidade Form','CidadeForm');
INSERT INTO system_program VALUES(44,'Cidade List','CidadeList');
INSERT INTO system_program VALUES(45,'Grupo Form','GrupoForm');
INSERT INTO system_program VALUES(46,'Grupo List','GrupoList');
INSERT INTO system_program VALUES(47,'Papel Form','PapelForm');
INSERT INTO system_program VALUES(48,'Papel List','PapelList');
INSERT INTO system_program VALUES(49,'Pessoa Form','PessoaForm');
INSERT INTO system_program VALUES(50,'Pessoa List','PessoaList');
INSERT INTO system_program VALUES(51,'Pessoa Form View','PessoaFormView');
INSERT INTO system_program VALUES(52,'Tipo Contrato Form','TipoContratoForm');
INSERT INTO system_program VALUES(53,'Tipo Contrato List','TipoContratoList');
INSERT INTO system_program VALUES(54,'Contrato Form','ContratoForm');
INSERT INTO system_program VALUES(55,'Contrato List','ContratoList');
INSERT INTO system_program VALUES(56,'Tipo Servico Form','TipoServicoForm');
INSERT INTO system_program VALUES(57,'Tipo Servico List','TipoServicoList');
INSERT INTO system_program VALUES(58,'Servico Form','ServicoForm');
INSERT INTO system_program VALUES(59,'Servico List','ServicoList');
INSERT INTO system_program VALUES(60,'Fatura Form','FaturaForm');
INSERT INTO system_program VALUES(61,'Fatura List','FaturaList');
INSERT INTO system_program VALUES(62,'Conta Receber Form','ContaReceberForm');
INSERT INTO system_program VALUES(63,'Conta Receber List','ContaReceberList');
INSERT INTO system_program VALUES(64,'Gera Faturas List','GeraFaturasList');
INSERT INTO system_program VALUES(65,'Gera Contas Receber List','GeraContasReceberList');
INSERT INTO system_program VALUES(66,'Conta Receber Quitacao List','ContaReceberQuitacaoList');
INSERT INTO system_program VALUES(67,'Contrato Dashboard','ContratoDashboard');
INSERT INTO system_program VALUES(68,'Fatura Dashboard','FaturaDashboard');
INSERT INTO system_program VALUES(69,'Financeiro Dashboard','FinanceiroDashboard');
INSERT INTO system_program VALUES(70,'Calendario Form','CalendarioForm');
INSERT INTO system_program VALUES(71,'Calendario View','CalendarioView');
INSERT INTO system_program VALUES(72,'Projeto Form','ProjetoForm');
INSERT INTO system_program VALUES(73,'Projeto List','ProjetoList');
INSERT INTO system_program VALUES(74,'Projeto Card List','ProjetoCardList');
INSERT INTO system_program VALUES(75,'Kanban View','KanbanView');
INSERT INTO system_program VALUES(76,'Kanban Fase Form','KanbanFaseForm');
INSERT INTO system_program VALUES(77,'Kanban Atividade Form','KanbanAtividadeForm');
INSERT INTO system_program VALUES(78,'System files diff','SystemFilesDiff');
INSERT INTO system_program VALUES(79,'System Information','SystemInformationView');

INSERT INTO system_unit VALUES(1,'Unit A','unit_a');
INSERT INTO system_unit VALUES(2,'Unit B','unit_b');

INSERT INTO system_user VALUES(1,'Administrator','admin','21232f297a57a5a743894a0e4a801fc3','admin@admin.net',10,NULL,'Y','N',NULL);
INSERT INTO system_user VALUES(2,'User','user','ee11cbb19052e40b07aac0ca060c23ee','user@user.net',7,NULL,'Y','N',NULL);

INSERT INTO system_user_unit VALUES(5,1,1);
INSERT INTO system_user_unit VALUES(6,1,2);
INSERT INTO system_user_unit VALUES(7,2,1);
INSERT INTO system_user_unit VALUES(8,2,2);

INSERT INTO system_user_group VALUES(3,1,1);

INSERT INTO system_group_program VALUES(1,1,1);
INSERT INTO system_group_program VALUES(2,1,2);
INSERT INTO system_group_program VALUES(3,1,3);
INSERT INTO system_group_program VALUES(4,1,4);
INSERT INTO system_group_program VALUES(5,1,5);
INSERT INTO system_group_program VALUES(6,1,6);
INSERT INTO system_group_program VALUES(7,1,8);
INSERT INTO system_group_program VALUES(8,1,9);
INSERT INTO system_group_program VALUES(9,1,11);
INSERT INTO system_group_program VALUES(10,1,14);
INSERT INTO system_group_program VALUES(11,1,15);
INSERT INTO system_group_program VALUES(20,1,21);
INSERT INTO system_group_program VALUES(25,1,26);
INSERT INTO system_group_program VALUES(26,1,27);
INSERT INTO system_group_program VALUES(27,1,28);
INSERT INTO system_group_program VALUES(28,1,29);
INSERT INTO system_group_program VALUES(30,1,31);
INSERT INTO system_group_program VALUES(31,1,32);
INSERT INTO system_group_program VALUES(32,1,33);
INSERT INTO system_group_program VALUES(33,1,34);
INSERT INTO system_group_program VALUES(34,1,35);
INSERT INTO system_group_program VALUES(36,1,36);
INSERT INTO system_group_program VALUES(37,1,37);
INSERT INTO system_group_program VALUES(38,1,38);
INSERT INTO system_group_program VALUES(39,1,39);
INSERT INTO system_group_program VALUES(40,1,40);
INSERT INTO system_group_program VALUES(41,1,41);
INSERT INTO system_group_program VALUES(42,3,41);
INSERT INTO system_group_program VALUES(43,1,42);
INSERT INTO system_group_program VALUES(44,3,42);
INSERT INTO system_group_program VALUES(45,1,10);
INSERT INTO system_group_program VALUES(46,1,43);
INSERT INTO system_group_program VALUES(47,3,43);
INSERT INTO system_group_program VALUES(48,1,44);
INSERT INTO system_group_program VALUES(49,3,44);
INSERT INTO system_group_program VALUES(50,1,45);
INSERT INTO system_group_program VALUES(51,3,45);
INSERT INTO system_group_program VALUES(52,1,46);
INSERT INTO system_group_program VALUES(53,3,46);
INSERT INTO system_group_program VALUES(54,1,47);
INSERT INTO system_group_program VALUES(55,3,47);
INSERT INTO system_group_program VALUES(56,1,48);
INSERT INTO system_group_program VALUES(57,3,48);
INSERT INTO system_group_program VALUES(58,1,49);
INSERT INTO system_group_program VALUES(59,3,49);
INSERT INTO system_group_program VALUES(60,1,50);
INSERT INTO system_group_program VALUES(61,3,50);
INSERT INTO system_group_program VALUES(62,1,51);
INSERT INTO system_group_program VALUES(63,3,51);
INSERT INTO system_group_program VALUES(68,1,54);
INSERT INTO system_group_program VALUES(69,5,54);
INSERT INTO system_group_program VALUES(70,1,55);
INSERT INTO system_group_program VALUES(71,5,55);
INSERT INTO system_group_program VALUES(72,1,52);
INSERT INTO system_group_program VALUES(73,5,52);
INSERT INTO system_group_program VALUES(74,1,53);
INSERT INTO system_group_program VALUES(75,5,53);
INSERT INTO system_group_program VALUES(76,1,56);
INSERT INTO system_group_program VALUES(77,4,56);
INSERT INTO system_group_program VALUES(78,1,57);
INSERT INTO system_group_program VALUES(79,4,57);
INSERT INTO system_group_program VALUES(80,1,58);
INSERT INTO system_group_program VALUES(81,4,58);
INSERT INTO system_group_program VALUES(82,1,59);
INSERT INTO system_group_program VALUES(83,4,59);
INSERT INTO system_group_program VALUES(84,1,60);
INSERT INTO system_group_program VALUES(85,6,60);
INSERT INTO system_group_program VALUES(86,1,61);
INSERT INTO system_group_program VALUES(87,6,61);
INSERT INTO system_group_program VALUES(88,1,62);
INSERT INTO system_group_program VALUES(89,7,62);
INSERT INTO system_group_program VALUES(90,1,63);
INSERT INTO system_group_program VALUES(91,7,63);
INSERT INTO system_group_program VALUES(92,1,64);
INSERT INTO system_group_program VALUES(93,5,64);
INSERT INTO system_group_program VALUES(94,1,65);
INSERT INTO system_group_program VALUES(95,6,65);
INSERT INTO system_group_program VALUES(96,1,66);
INSERT INTO system_group_program VALUES(97,7,66);
INSERT INTO system_group_program VALUES(98,1,67);
INSERT INTO system_group_program VALUES(99,5,67);
INSERT INTO system_group_program VALUES(100,1,68);
INSERT INTO system_group_program VALUES(101,6,68);
INSERT INTO system_group_program VALUES(102,1,69);
INSERT INTO system_group_program VALUES(103,7,69);
INSERT INTO system_group_program VALUES(104,1,70);
INSERT INTO system_group_program VALUES(105,8,70);
INSERT INTO system_group_program VALUES(106,1,71);
INSERT INTO system_group_program VALUES(107,8,71);
INSERT INTO system_group_program VALUES(108,1,72);
INSERT INTO system_group_program VALUES(109,8,72);
INSERT INTO system_group_program VALUES(110,1,73);
INSERT INTO system_group_program VALUES(111,8,73);
INSERT INTO system_group_program VALUES(112,1,74);
INSERT INTO system_group_program VALUES(113,8,74);
INSERT INTO system_group_program VALUES(114,1,75);
INSERT INTO system_group_program VALUES(115,8,75);
INSERT INTO system_group_program VALUES(116,1,76);
INSERT INTO system_group_program VALUES(117,8,76);
INSERT INTO system_group_program VALUES(118,1,77);
INSERT INTO system_group_program VALUES(119,8,77);
INSERT INTO system_group_program VALUES(120,1,78);
INSERT INTO system_group_program VALUES(121,1,79);

INSERT INTO system_user_program VALUES(1,2,7);
CREATE INDEX sys_user_program_idx ON system_user(frontpage_id);
CREATE INDEX sys_user_group_group_idx ON system_user_group(system_group_id);
CREATE INDEX sys_user_group_user_idx ON system_user_group(system_user_id);
CREATE INDEX sys_group_program_program_idx ON system_group_program(system_program_id);
CREATE INDEX sys_group_program_group_idx ON system_group_program(system_group_id);
CREATE INDEX sys_user_program_program_idx ON system_user_program(system_program_id);
CREATE INDEX sys_user_program_user_idx ON system_user_program(system_user_id);

COMMIT;
