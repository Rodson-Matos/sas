begin; 

CREATE TABLE curso( 
      curso_id  integer generated by default as identity primary key     NOT NULL , 
      nome_curso blob sub_type 1    NOT NULL , 
      tipo_curso blob sub_type 1    NOT NULL , 
      modalidade blob sub_type 1    NOT NULL , 
      turno_curso blob sub_type 1    NOT NULL , 
 PRIMARY KEY (curso_id)); 

 CREATE TABLE curso_disciplina( 
      id  integer generated by default as identity primary key     NOT NULL , 
      curso_id integer    NOT NULL , 
      disciplina_id integer    NOT NULL , 
 PRIMARY KEY (id)); 

 CREATE TABLE curso_horario( 
      id  integer generated by default as identity primary key     NOT NULL , 
      horarios_id integer    NOT NULL , 
      curso_id integer    NOT NULL , 
 PRIMARY KEY (id)); 

 CREATE TABLE disciplina( 
      disciplina_id  integer generated by default as identity primary key     NOT NULL , 
      nome_disc blob sub_type 1    NOT NULL , 
      carga_h integer  (3)    NOT NULL , 
 PRIMARY KEY (disciplina_id)); 

 CREATE TABLE horarios( 
      horarios_id  integer generated by default as identity primary key     NOT NULL , 
      horario time    NOT NULL , 
      dia_sem blob sub_type 1   , 
 PRIMARY KEY (horarios_id)); 

 CREATE TABLE salas( 
      sala_id  integer generated by default as identity primary key     NOT NULL , 
      numero_sala integer    NOT NULL , 
      capacidade_sala integer  (2)    NOT NULL , 
      tipo_sala blob sub_type 1   , 
 PRIMARY KEY (sala_id)); 

 CREATE TABLE turma( 
      turma_id  integer generated by default as identity primary key     NOT NULL , 
      tipo_turma blob sub_type 1    NOT NULL , 
      n_alunos integer  (2)    NOT NULL , 
      curso_id integer    NOT NULL , 
 PRIMARY KEY (turma_id)); 

  
 ALTER TABLE salas ADD UNIQUE (numero_sala);
  
 ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_2 FOREIGN KEY (disciplina_id) references disciplina(disciplina_id); 
ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_1 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_2 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_1 FOREIGN KEY (horarios_id) references horarios(horarios_id); 
ALTER TABLE turma ADD CONSTRAINT fk_turma_1 FOREIGN KEY (curso_id) references curso(curso_id); 

  
 
 commit;