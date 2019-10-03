begin; 

PRAGMA foreign_keys=OFF; 

CREATE TABLE curso( 
      curso_id  INTEGER    NOT NULL  , 
      nome_curso text   NOT NULL  , 
      tipo_curso text   NOT NULL  , 
      modalidade text   NOT NULL  , 
      turno_curso text   NOT NULL  , 
 PRIMARY KEY (curso_id)); 

 CREATE TABLE curso_disciplina( 
      id  INTEGER    NOT NULL  , 
      curso_id int   NOT NULL  , 
      disciplina_id int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(disciplina_id) REFERENCES disciplina(disciplina_id),
FOREIGN KEY(curso_id) REFERENCES curso(curso_id)); 

 CREATE TABLE curso_horario( 
      id  INTEGER    NOT NULL  , 
      horarios_id int   NOT NULL  , 
      curso_id int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(curso_id) REFERENCES curso(curso_id),
FOREIGN KEY(horarios_id) REFERENCES horarios(horarios_id)); 

 CREATE TABLE disciplina( 
      disciplina_id  INTEGER    NOT NULL  , 
      nome_disc text   NOT NULL  , 
      carga_h int  (3)   NOT NULL  , 
 PRIMARY KEY (disciplina_id)); 

 CREATE TABLE horarios( 
      horarios_id  INTEGER    NOT NULL  , 
      horario text   NOT NULL  , 
      dia_sem text   , 
 PRIMARY KEY (horarios_id)); 

 CREATE TABLE salas( 
      sala_id  INTEGER    NOT NULL  , 
      numero_sala int   NOT NULL  , 
      capacidade_sala int  (2)   NOT NULL  , 
      tipo_sala text   , 
 PRIMARY KEY (sala_id)); 

 CREATE TABLE turma( 
      turma_id  INTEGER    NOT NULL  , 
      tipo_turma text   NOT NULL  , 
      n_alunos int  (2)   NOT NULL  , 
      curso_id int   NOT NULL  , 
 PRIMARY KEY (turma_id),
FOREIGN KEY(curso_id) REFERENCES curso(curso_id)); 

  
 CREATE UNIQUE INDEX idx_salas_numero_sala ON salas(numero_sala);
 
  
 
 commit;