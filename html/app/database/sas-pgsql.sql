begin; 

CREATE TABLE curso( 
      curso_id  SERIAL    NOT NULL  , 
      nome_curso text   NOT NULL  , 
      tipo_curso text   NOT NULL  , 
      modalidade text   NOT NULL  , 
      turno_curso text   NOT NULL  , 
 PRIMARY KEY (curso_id)); 

 CREATE TABLE curso_disciplina( 
      id  SERIAL    NOT NULL  , 
      curso_id integer   NOT NULL  , 
      disciplina_id integer   NOT NULL  , 
 PRIMARY KEY (id)); 

 CREATE TABLE curso_horario( 
      id  SERIAL    NOT NULL  , 
      horarios_id integer   NOT NULL  , 
      curso_id integer   NOT NULL  , 
 PRIMARY KEY (id)); 

 CREATE TABLE disciplina( 
      disciplina_id  SERIAL    NOT NULL  , 
      nome_disc text   NOT NULL  , 
      carga_h integer   NOT NULL  , 
 PRIMARY KEY (disciplina_id)); 

 CREATE TABLE horarios( 
      horarios_id  SERIAL    NOT NULL  , 
      horario time   NOT NULL  , 
      dia_sem text   , 
 PRIMARY KEY (horarios_id)); 

 CREATE TABLE salas( 
      sala_id  SERIAL    NOT NULL  , 
      numero_sala integer   NOT NULL  , 
      capacidade_sala integer   NOT NULL  , 
      tipo_sala text   , 
 PRIMARY KEY (sala_id)); 

 CREATE TABLE turma( 
      turma_id  SERIAL    NOT NULL  , 
      tipo_turma text   NOT NULL  , 
      n_alunos integer   NOT NULL  , 
      curso_id integer   NOT NULL  , 
 PRIMARY KEY (turma_id)); 

  
 ALTER TABLE salas ADD UNIQUE (numero_sala);
  
 ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_2 FOREIGN KEY (disciplina_id) references disciplina(disciplina_id); 
ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_1 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_2 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_1 FOREIGN KEY (horarios_id) references horarios(horarios_id); 
ALTER TABLE turma ADD CONSTRAINT fk_turma_1 FOREIGN KEY (curso_id) references curso(curso_id); 

  
 
 commit;