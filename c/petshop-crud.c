#include <stdio.h>   // Para funções de entrada e saída (printf, scanf, fopen etc.)
#include <stdlib.h>  // Para funções de uso geral (system, exit etc.)
#include <string.h>  // Para manipulação de strings (strlen, strcmp, strcspn etc.)
#include <ctype.h> 	 // Para verificação de caracteres (isupper, isdigit, isalnum etc.)
#include <locale.h>  // Para permitir as acentuações do português (setlocale)
#include <stdbool.h> // Para usar o tipo booleano (bool, true, false)

#define ARQUIVO "../auth/data/clientes.txt"
#define ARQUIVO_PETS "../auth/data/pets.txt"

#define MAX_USUARIO 10
#define MIN_USUARIO 5
#define MAX_SENHA 49
#define MAX_NOME_PET 20
#define MAX_IDADE 15
#define MAX_ESPECIE 15
#define MAX_RACA 50

// Variáveis para o pet do cliente
typedef struct {
	char nome[MAX_NOME_PET + 1];
	char idade[MAX_IDADE + 1];
	char especie[MAX_ESPECIE + 1];
	char raca[MAX_RACA + 1];
} Pet;

// Protótipos das funções para que o compilador as conheça
void limparBuffer();
void limparTela();
int contemMinuscula(const char *senha);
int contemMaiuscula(const char *senha);
int contemNumero(const char *senha);
int contemCaractereEspecial(const char *senha);
bool usuarioExiste(const char *usuarioTemp);
bool fazerLogin();
bool criarUsuario();
void adicionarPet();
void listarPets();
void atualizarPet();
void removerPet();

// Para armazenar o nome de usuário atualmente logado
char usuarioLogado[MAX_USUARIO + 1] = "";

// Função para limpar o buffer do teclado
void limparBuffer() {
	int c;
	while ((c = getchar()) != '\n' && c != EOF);
}

// Função para limpar a tela
void limparTela() {
	system("cls || clear");
}

// Verificações da senha
int contemMinuscula(const char *senha) {
	int i;
	
	for (i = 0; senha[i] != '\0'; i++) {
		if (islower(senha[i])) {
			return 1;
		}
	}
	return 0;
}

int contemMaiuscula(const char *senha) {
	int i; 
	for (i = 0; senha[i] != '\0'; i++) {
		if (isupper(senha[i])) {
			return 1;
		}
	}
	return 0;
}

int contemNumero(const char *senha) {
	int i; 
	for (i = 0; senha[i] != '\0'; i++) {
		if (isdigit(senha[i])) {
			return 1;
		}
	}
	return 0;
}

int contemCaractereEspecial(const char *senha) {
	int i; 
	for (i = 0; senha[i] != '\0'; i++) {
		// Se não for alfanumérico (letra ou número), é especial
		if(!isalnum(senha[i])) {
			return 1;
		}
	}
	return 0;
}

// Função para checar se o usuário já está no arquivo
bool usuarioExiste(const char *usuarioTemp){
	// Abre o arquivo de clientes para consultar o nome de usuario
	FILE* fp = fopen(ARQUIVO, "r");
	if (fp == NULL) {
		// Se o arquivo não existe, o usuário não existe
		return false;
	}	

	char line[256];
	char file_username[MAX_USUARIO + 1];

	// Lendo linha por linha
	while (fgets(line, sizeof(line), fp)) {
		// Tenta extrair somente o nome de usuário (até o ';')
		// %10[^;] lê até 10 caracteres que não sejam ';'
		if (sscanf(line, "%*[^;];%*[^;];%*[^;];%*[^;];%10[^;]", file_username) == 1) {			
			// Remove quebras de linhas, se houver
			file_username[strcspn(file_username, "\r\n")] = '\0';
			
			// Se o usuário informado for igual a um do arquivo, ele existe
			if (strcmp(usuarioTemp, file_username) == 0) {
				fclose(fp);
				return true;
			}
		}
	}
	
	fclose(fp);
	return false; // Usuário não encontrado no arquivo
}

bool fazerLogin(){
	char usuarioTemp[MAX_USUARIO + 1];	
	char senhaTemp[MAX_SENHA + 1];
	bool sucesso = false;
	
	printf("\n--- LOGIN ---\n");

	// Laço de repetição para múltiplas tentativas de login
	do {
        FILE *fp = NULL;

		// Verifica se o arquivo de clientes existe
		fp = fopen(ARQUIVO, "r");
		if (fp == NULL) {
			printf("\n(!) Nenhum cliente foi cadastrado até o momento. Crie uma conta primeiro.\n");
			limparTela();
			return false;
		}
		fclose(fp); // Fecha a checagem inicial

		printf("> Digite seu nome de usuário (0 para voltar): ");
		fgets(usuarioTemp, sizeof(usuarioTemp), stdin);
		usuarioTemp[strcspn(usuarioTemp, "\n")] = '\0'; // Remove o \n

		if (strcmp(usuarioTemp, "0") == 0) {
			limparTela();
			return false;
		}

		// Checa se o usuário existe antes de pedir a senha
		if (!usuarioExiste(usuarioTemp)) {
			printf("\n(!) Este nome de usuário não existe. Tente novamente.\n");
			continue; // Volta para o início do loop (pedir usuário de novo)
		}

		printf("> Digite sua senha: ");
		fgets(senhaTemp, sizeof(senhaTemp), stdin);
		senhaTemp[strcspn(senhaTemp, "\n")] = '\0'; // Remove o \n
		
		// Reabre o arquivo para fazer a busca completa (usuário e senha)
		fp = fopen(ARQUIVO, "r");
		if (fp == NULL) {
			// Este erro só ocorreria se o arquivo fosse deletado após a checagem inicial
			printf("\n(!) Erro ao acessar o arquivo 'clientes.txt'.\n");	
			return false;
		}
		
		// Verifica se o usuário e senha informados batem com algum existente no arquivo
		char line[256];
		char file_username[MAX_USUARIO + 1];
		char file_senha[MAX_SENHA + 1];

		while (fgets(line, sizeof(line), fp)) {
			// Tenta ler o usuário e a senha até o ';' e o '\n' respectivamente
			if (sscanf(line, "%*[^;];%*[^;];%*[^;];%*[^;];%10[^;];%49[^\r\n]", file_username, file_senha) == 2) {
				if (strcmp(usuarioTemp, file_username) == 0 && strcmp(senhaTemp, file_senha) == 0 ) {
					sucesso = true;
                    strcpy(usuarioLogado, usuarioTemp); 
					break; // Sai do while
				}
			}
		}
		
		fclose(fp); // Fecha o arquivo

		if(sucesso){
			// Se o login for um sucesso, sai do loop
		} else {
			printf("\n(!) Credenciais inválidas. Tente novamente.\n");
		}
	} while(!sucesso); // Continua tentando até o login ser um sucesso

	limparTela();
	return sucesso;
}

bool criarUsuario() {
	char usuarioTemp[MAX_USUARIO + 1];
	char senhaTemp[MAX_SENHA + 1];
	bool erros;
	
	printf("\n--- CRIAR CONTA ---\n");
	
	// Verifica se o arquivo já existe, senão, cria ele
	FILE* fp_check = fopen(ARQUIVO, "a");
	if (fp_check == NULL) {
		printf("\n(!) Não foi possível criar/abrir o arquivo '%s'.\n", ARQUIVO);
		limparTela();
		return false;
	}
	fclose(fp_check);
	
	// Loop para garantir que o nome de usuário seja válido
	do {
		printf("> Digite seu nome de usuário: "); 
		fgets(usuarioTemp, sizeof(usuarioTemp), stdin);
		usuarioTemp[strcspn(usuarioTemp, "\n")] = '\0'; // Remove o \n

		// Verifica o tamanho
		if (strlen(usuarioTemp) < MIN_USUARIO || strlen(usuarioTemp) > MAX_USUARIO) {
			printf("\n(!) O nome de usuário deve ter entre %d e %d caracteres.\n", MIN_USUARIO, MAX_USUARIO);
			continue; // Volta para o início do loop
		}
		
		// Verifica se o usuário já existe
		if (usuarioExiste(usuarioTemp)) {
			printf("\n(!) '%s' já existe. Escolha outro.\n\n", usuarioTemp);
			continue; // Volta para o início do loop
		}
		
		break; // Sai do loop se for válido
		
	} while(true);


	// Solicita a senha e valida os critérios
	do {
		erros = false;

		printf("> Digite sua senha: ");
		fgets(senhaTemp, sizeof(senhaTemp), stdin);
		senhaTemp[strcspn(senhaTemp, "\n")] = '\0'; // Remove o \n

		if (strlen(senhaTemp) == 0) {
			printf("\n(!) É necessário informar uma senha.\n");
			erros = true;
		} else {
			// Verificações da senha
			if (!contemMinuscula(senhaTemp)) {
				printf("(!) A senha deve conter pelo menos uma letra minúscula.\n");
				erros = true;
			}
			if (!contemMaiuscula(senhaTemp)) {
				printf("(!) A senha deve conter pelo menos uma letra maiúscula.\n");
				erros = true;
			}
			if (!contemNumero(senhaTemp)) {
				printf("(!) A senha deve conter pelo menos um número.\n");
				erros = true;
			}
			if (!contemCaractereEspecial(senhaTemp)) {
				printf("(!) A senha deve conter pelo menos um caractere especial.\n");
				erros = true;
			}
		}
	} while (erros);

	// Salva o novo usuário no arquivo
	FILE* fp = fopen(ARQUIVO, "a");
	if (fp == NULL) {
		printf("\n(!) Ocorreu um erro ao abrir/criar o arquivo '%s' para escrita.\n", ARQUIVO);
		limparTela();
		return false;
	}

	// Formato: usuario;senha\n
	fprintf(fp, "-;-;-;-;%s;%s\n", usuarioTemp, senhaTemp);
	fclose(fp);

	printf("\n(*) Conta criada com sucesso! Você pode fazer login agora.\n");
	printf("Pressione Enter para continuar...");
	getchar();
	return true;
}

// Início da área CRUD dos pets
void menuPetshop() {
	int escolha;
    char temp_user[MAX_USUARIO + 1];
    int i = 0;

    // Copia e converte para maiúsculas (apenas para exibição)
    strcpy(temp_user, usuarioLogado);
    while(temp_user[i]) {
        temp_user[i] = toupper(temp_user[i]);
        i++;
    }
	
	do {
		limparTela();
		printf("--- PETS DE %s ---\n", temp_user); 
		printf("1) Adicionar pet\n");
		printf("2) Listar pets\n");
		printf("3) Atualizar pet*\n");
		printf("4) Remover pet*\n");
		printf("0) Sair da conta\n");
		printf("> Escolha uma opção: ");

		if (scanf("%d", &escolha) != 1) {
			printf("\n(!) Entrada inválida. Tente novamente.\n");
			printf("Pressione Enter para continuar...");
			getchar();
			continue;
		}
		limparBuffer();

		switch (escolha) {
			case 1:
				adicionarPet();
				break;
			case 2:
				listarPets();
				break;
			case 3:
				printf("\n(*) Em desenvolvimento...\n");
				printf("Pressione Enter para continuar...");
				getchar();
				break;
			case 4:
				printf("\n(*) Em desenvolvimento...\n");
				printf("Pressione Enter para continuar...");
				getchar();
				break;
			case 0:
				printf("\n(*) Desconectando de \"%s\"...\n", usuarioLogado);
				strcpy(usuarioLogado, ""); // Limpa o usuário logado
				printf("Pressione Enter para continuar...");
				getchar();
				break;
			default:
				printf("\n(!) Opção inválida. Tente novamente.\n");
				printf("Pressione Enter para continuar...");
				getchar();
				break;
		}
	} while (escolha != 0);
}

void adicionarPet() {
	Pet novoPet;
	
	limparTela();
	printf("--- ADICIONAR NOVO PET ---\n");
	
	printf("> Nome do pet: ");
	fgets(novoPet.nome, sizeof(novoPet.nome), stdin);
	novoPet.nome[strcspn(novoPet.nome, "\n")] = '\0';

    printf("> Idade (em anos): ");
    fgets(novoPet.idade, sizeof(novoPet.idade), stdin);
    novoPet.idade[strcspn(novoPet.idade, "\n")] = '\0';

	printf("> Espécie: ");
	fgets(novoPet.especie, sizeof(novoPet.especie), stdin);
	novoPet.especie[strcspn(novoPet.especie, "\n")] = '\0';

	printf("> Raça: ");
	fgets(novoPet.raca, sizeof(novoPet.raca), stdin);
	novoPet.raca[strcspn(novoPet.raca, "\n")] = '\0';

	// Abre o arquivo de pets para adicionar
	FILE* fp = fopen(ARQUIVO_PETS, "a");
	if (fp == NULL) {
		printf("\n(!) Erro ao abrir/criar o arquivo de pets.\n");
		printf("Pressione Enter para continuar...");
		getchar();
		return;
	}

	// Formato: usuarioLogado;nome_pet;idade;especie;raca\n
	fprintf(fp, "%s;%s;%s;%s;%s\n",	
			usuarioLogado,	
			novoPet.nome,
            novoPet.idade, 
			novoPet.especie,	
			novoPet.raca);
			
	fclose(fp);

	printf("\n(*) '%s' foi adicionado com sucesso!\n", novoPet.nome);
	printf("Pressione Enter para continuar...");
	getchar();
}

void listarPets() {
    char temp_user[MAX_USUARIO + 1];
    int i = 0;
    
    // Joga o nome do usuário para maiúsculo apenas para exibição
    strcpy(temp_user, usuarioLogado);
    while(temp_user[i]) {
        temp_user[i] = toupper(temp_user[i]);
        i++;
    }

	limparTela();
	printf("--- PETS DE %s ---\n", temp_user);	
	
	FILE* fp = fopen(ARQUIVO_PETS, "r");
	if (fp == NULL) {
		// Se o arquivo de pets não existir ainda, exibir:
		printf("\n(!) Nenhum pet está cadastrado até o momento.\n");
		printf("Pressione Enter para continuar...");
		getchar();
		return;
	}

	char line[256];
	char user_file[MAX_USUARIO + 1];
	Pet pet_lido;
	int contador = 0;
    
	while (fgets(line, sizeof(line), fp)) {
		// Lê no formato: usuario;nome_pet;idade;especie;raca
		if (sscanf(line, "%10[^;];%20[^;];%15[^;];%15[^;];%50[^\r\n]",	
					user_file,	
					pet_lido.nome,
					pet_lido.idade,	
					pet_lido.especie,	
					pet_lido.raca) == 5) {	

			// Checa se o pet pertence ao usuário logado
			if (strcmp(usuarioLogado, user_file) == 0) {
                if (contador == 0) {
                    printf("\n%-2s | %-15s | %-5s | %-10s | %-10s\n",
					        "ID", "Nome", "Idade", "Espécie", "Raça");
					printf("-----------------------------------------------------------------\n");
                }
                
				contador++;
				printf("%-2d | %-15s | %-5s | %-10s | %-10s\n",	
						contador,	
						pet_lido.nome,
						pet_lido.idade,	
						pet_lido.especie,	
						pet_lido.raca);
			}
		}
	}

	fclose(fp);

    // Verifica a contagem após a leitura completa
	if (contador == 0) {
		printf("(!) Nenhum pet foi encontrado para este usuário.\n");
	}
	
	printf("\nPressione Enter para continuar...");
	getchar();
}

void atualizarPet() {
	// criar aqui
}

void removerPet() {
	// criar aqui
}
// Fim da área CRUD dos pets

int main() {
	int escolha;
	bool logado = false;
	
	setlocale(LC_ALL, "Portuguese");

	// Loop principal do menu
	while(true) {
		limparTela(); // Limpa a tela a cada iteração do menu
		printf("-- MENU PETSHOP --\n");
		printf("1) Fazer login\n");
		printf("2) Criar conta\n");
		printf("0) Sair\n");
		printf("> Escolha uma opção: ");

		// Leitura da escolha do menu
		if (scanf("%d", &escolha) != 1) {
			printf("\n(!) Entrada inválida. Tente novamente.\n");
			limparBuffer(); // Limpa o buffer se for uma entrada não numérica
			printf("Pressione Enter para continuar...");
			getchar();
			continue; // Volta para o início do loop
		}
		limparBuffer();

		
		switch (escolha) {
			case 1:
				logado = fazerLogin();
				if(logado) {
					menuPetshop();	
					logado = false; // Retorna 'false' quando o usuário sair do menu CRUD de pets
				}
				break;
			case 2:
				criarUsuario(); // Cria o usuário
				break;
			case 0:
				printf("\nEncerrando...");
				break;
			default:
				printf("\n(!) Opção inválida. Tente novamente.\n");
				printf("Pressione Enter para continuar...");
				getchar();
				break;
		}

		if (escolha == 0 || logado) {
			break;
		}
	}

	return 0;
}
