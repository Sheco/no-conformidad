create table nocon_doc (
 id integer unsigned primary key auto_increment,
 folio string,
 fecha datetime,
 creador_usr_id integer unsigned,
 departamento_id integer unsigned,
 tipo_id integer unsigned foreign key references noco_tipos(id),
 descripcion text,
 status_id integer unsigned foreign key references noco_status(id),
 asignado_usr_id integer unsigned
);

alter table users add nocon_contador integer not null default 1;

create table nocon_tipos (
 id integer unsigned primary key auto_increment,
 nombre varchar(100)
);

insert into nocon_tipos values 
 (1, 'Incumplimiento'), 
 (2, 'Situación de riesgo'),
 (3, 'Accidente / Avería');

create table nocon_status (
 id integer unsigned primary key auto_increment,
 codigo varchar(100),
 nombre varchar(100),
 index(codigo)
);

insert into nocon_status values
 (1, 'inicio', 'Inicio'),
 (2, 'pendiente-propuesta', 'Pendiente propuesta'),
 (3, 'pendiente-revision', 'Pendiente revisión'),
 (4, 'en-progreso', 'En progreso'),
 (5, 'completada', 'Completada'),
 (6, 'verificada', 'Verificada'),
 (7, 'cerrada', 'Cerrada')
;

create table nocon_propuesta (
 id integer unsigned primary key auto_increment,
 doc_id integer unsigned foreign key references noco_doc(id),
 descripcion text,
 usr_id integer unsigned

 retro text,
 retro_usr_id integer unsigned
);
