begin; 

CREATE TABLE curso( 
      curso_id  INT IDENTITY    NOT NULL  , 
      nome_curso nvarchar(max)   NOT NULL  , 
      tipo_curso nvarchar(max)   NOT NULL  , 
      modalidade nvarchar(max)   NOT NULL  , 
      turno_curso nvarchar(max)   NOT NULL  , 
 PRIMARY KEY (curso_id)); 

 CREATE TABLE curso_disciplina( 
      id  INT IDENTITY    NOT NULL  , 
      curso_id int   NOT NULL  , 
      disciplina_id int   NOT NULL  , 
 PRIMARY KEY (id)); 

 CREATE TABLE curso_horario( 
      id  INT IDENTITY    NOT NULL  , 
      horarios_id int   NOT NULL  , 
      curso_id int   NOT NULL  , 
 PRIMARY KEY (id)); 

 CREATE TABLE disciplina( 
      disciplina_id  INT IDENTITY    NOT NULL  , 
      nome_disc nvarchar(max)   NOT NULL  , 
      carga_h int  (3)   NOT NULL  , 
 PRIMARY KEY (disciplina_id)); 

 CREATE TABLE horarios( 
      horarios_id  INT IDENTITY    NOT NULL  , 
      horario time   NOT NULL  , 
      dia_sem nvarchar(max)   , 
 PRIMARY KEY (horarios_id)); 

 CREATE TABLE salas( 
      sala_id  INT IDENTITY    NOT NULL  , 
      numero_sala int   NOT NULL  , 
      capacidade_sala int  (2)   NOT NULL  , 
      tipo_sala nvarchar(max)   , 
 PRIMARY KEY (sala_id)); 

 CREATE TABLE turma( 
      turma_id  INT IDENTITY    NOT NULL  , 
      tipo_turma nvarchar(max)   NOT NULL  , 
      n_alunos int  (2)   NOT NULL  , 
      curso_id int   NOT NULL  , 
 PRIMARY KEY (turma_id)); 

  
 ALTER TABLE salas ADD UNIQUE (numero_sala);
  
 ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_2 FOREIGN KEY (disciplina_id) references disciplina(disciplina_id); 
ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_1 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_2 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_1 FOREIGN KEY (horarios_id) references horarios(horarios_id); 
ALTER TABLE turma ADD CONSTRAINT fk_turma_1 FOREIGN KEY (curso_id) references curso(curso_id); 

  
 
 commit;