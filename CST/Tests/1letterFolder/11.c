nclude "interpret.h"
#define typeNUMERIC        44
#define typeSTRING         34
#define typeBOOLEAN        51
#define typeFUNC           2
#define typeNULL           7
#define maxSizeWhile	   10

int if_true;
int while_func;
tListInstr *Itmp;
tInstr *I_tmp;
//void *whilefce[maxSizeWhile];
int size = maxSizeWhile;

/*TODO:SCANF,RETURN,FUNCTION*/
int executeInstr(tListInstr *instrList,tInstr *I)
{
  int l = -1;
  void **whilefce = malloc(10*sizeof(tListItem));
    //printf("%lf",((tNodePtr)I->addr1)->data->value.intVal);
    switch (I->instType)
    {
    case I_ADD:
        /*instrukce pro scitani dvou operandu
         * soucet scitanec scitanec[real/int int/real int/real]
         */
	if ((((((tNodePtr)I->addr2)->data->type)) == typeNUMERIC) && (((((tNodePtr)I->addr3)->data->type)) == typeNUMERIC))
	{
        ((((tNodePtr)I->addr1)->data->type)) = typeNUMERIC;
        ((tNodePtr)I->addr1)->data->value.intVal= (((tNodePtr)I->addr2)->data->value.intVal) + (((tNodePtr)I->addr3)->data->value.intVal);
	}
	else if((((((tNodePtr)I->addr2)->data->type)) == typeSTRING) && (((((tNodePtr)I->addr3)->data->type)) == typeSTRING))
	{
	  ((((tNodePtr)I->addr1)->data->type)) = typeSTRING;
	  int i = 0;
	strInit(&(((tNodePtr)I->addr1)->data->value.string));
        while(((((tNodePtr)I->addr2)->data->value.string)->str[i] != '\0'))
        {
            addCharToWord(&(((tNodePtr)I->addr1)->data->value.string), ((((tNodePtr)I->addr2)->data->value.string)->str[i]));
            i++;
        }
        i=0;
        while(((((tNodePtr)I->addr3)->data->value.string)->str[i] != '\0'))
        {
            addCharToWord(&(((tNodePtr)I->addr1)->data->value.string), ((((tNodePtr)I->addr3)->data->value.string)->str[i]));
            i++;
        }
        //printf("%s",(((tNodePtr)I->addr1)->data->value.string)->str);
	}
        //printf("soucet probehl uspesne %f\n",((tNodePtr)I->addr1)->data->value.intVal);
        break;
    //case I_CONCATENATE://TEST:FUNGUJE
        /*[string] [string] [string]
         * prvni je vysledek, druhy je prvni slovo a treti je slovo, ktere pridavame ke konci prvniho slova
         */
       /* ((((tNodePtr)I->addr1)->data->type)) = typeSTRING;
        int i = 0;
	strInit(&(((tNodePtr)I->addr1)->data->value.string));
        while(((((tNodePtr)I->addr2)->data->value.string)->str[i] != '\0'))
        {
            addCharToWord(&(((tNodePtr)I->addr1)->data->value.string), ((((tNodePtr)I->addr2)->data->value.string)->str[i]));
            i++;
        }
        i=0;
        while(((((tNodePtr)I->addr3)->data->value.string)->str[i] != '\0'))
        {
            addCharToWord(&(((tNodePtr)I->addr1)->data->value.string), ((((tNodePtr)I->addr3)->data->value.string)->str[i]));
            i++;
        }*/
        //printf("%s",(((tNodePtr)I->addr1)->data->value.string)->str);
       // break;
    case I_SORT://TEST:FUNGUJE
        /*sort bere char* jako parametr a vraci serazeny string
         prvni parametr jenom pro alokaci, druhy pro dany string*/
        ((tNodePtr)I->addr2)->data->type = typeSTRING;
        //(((tNodePtr)I->addr1)->data->value.string)->str=sort((((tNodePtr)I->addr2)->data->value.string)->str);
        break;
    case I_NUMERIC: //TEST:FUNGUJE
        /*vysledek je v 1cce, vstupni parametr v dvojce*/
        (((tNodePtr)I->addr1)->data->type) = typeNUMERIC;
        //((tNodePtr)I->addr1)->data->value.intVal = numeric((((tNodePtr)I->addr2)->data->value.string));
        break;

    case I_INPUT: //TEST:FUNGUJE
        /*vysledek je v 1cce, vstupni parametr zadny*/
        (((tNodePtr)I->addr1)->data->type) = typeSTRING;
        (((tNodePtr)I->addr1)->data->value.string) = malloc(sizeof(tDataPtr));
        (*((tNodePtr)I->addr1)->data->value.string) = input();
        break;
   // case I_STRING_MULT: //TEST:FUNGUJE
        /*[string] [string] [real]
         * prvni je vysledek operaci, druhe je zadany string a treti je pocet, kolikrat se to ma znasobit*/
      /*  (((tNodePtr)I->addr1)->data->type) = typeSTRING;
        if (((tNodePtr)I->addr3)->data->value.intVal == 0.0)
        {
            (((tNodePtr)I->addr1)->data->value.string)->str = "";
        }
        else if (((tNodePtr)I->addr3)->data->value.intVal > 0.0)
        {
            for (int i=0; i< (int)((tNodePtr)I->addr3)->data->value.intVal; i++)
            {
                int k = 0;
                while(((((tNodePtr)I->addr2)->data->value.string)->str[k] != '\0'))
                {
                    addCharToWord(&(((tNodePtr)I->addr1)->data->value.string), ((((tNodePtr)I->addr2)->data->value.string)->str[k]));
                    k++;
                }
            }
        }
        //printf("%s",(((tNodePtr)I->addr1)->data->value.string)->str);
        break;*/
    case I_SUB://TEST:FUNGUJE
        /*instrukce pro scitani dvou operandu
         * rozdil prvni cislo - druhe cislo[real/int int/real int/real]
         */
	if ((((((tNodePtr)I->addr2)->data->type)) == typeNUMERIC) && (((((tNodePtr)I->addr3)->data->type)) == typeNUMERIC))
	{
        (((tNodePtr)I->addr1)->data->type) = typeNUMERIC;
        ((tNodePtr)I->addr1)->data->value.intVal= (((tNodePtr)I->addr2)->data->value.intVal) - (((tNodePtr)I->addr3)->data->value.intVal);
	}
        break;
    case I_END_IF:
        /* nic se nebude dit*/
        break;
	    case I_NOT_EQUAL:
        /*porovnani mensi
               * [bool] [int/real/nill] [int/real/nill]
               * [bool] [string/nill] [string/nill]
               */
        //string compare
        if (((((tNodePtr)I->addr2)->data->type == typeSTRING) && (((tNodePtr)I->addr3)->data->type == typeSTRING))
                || ((((tNodePtr)I->addr2)->data->type == typeSTRING) && (((tNodePtr)I->addr3)->data->type == typeNULL))
                || ((((tNodePtr)I->addr2)->data->type == typeNULL) && (((tNodePtr)I->addr3)->data->type == typeSTRING)))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            int tmp_match = strcmp((((tNodePtr)I->addr2)->data->value.string)->str,(((tNodePtr)I->addr3)->data->value.string)->str);
            if (tmp_match == 0)
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
            else
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
        }
        else if ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && (((tNodePtr)I->addr3)->data->type == typeNUMERIC))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            if (((tNodePtr)I->addr2)->data->value.intVal == (((tNodePtr)I->addr3)->data->value.intVal))
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
            else
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
        }
        else
        {
	    exit(SEMANTIC_OTHER_ERROR);
            return INTERPRET_ERROR;
        }
        break;
	
    case I_IF_EQUAL:
        /*porovnani mensi
               * [bool] [int/real/nill] [int/real/nill]
               * [bool] [string/nill] [string/nill]
               */
        //string compare
        		printf("abc");
        if (((((tNodePtr)I->addr2)->data->type == typeSTRING) && (((tNodePtr)I->addr3)->data->type == typeSTRING))
                || ((((tNodePtr)I->addr2)->data->type == typeSTRING) && (((tNodePtr)I->addr3)->data->type == typeNULL))
                || ((((tNodePtr)I->addr2)->data->type == typeNULL) && (((tNodePtr)I->addr3)->data->type == typeSTRING)))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            int tmp_match = strcmp((((tNodePtr)I->addr2)->data->value.string)->str,(((tNodePtr)I->addr3)->data->value.string)->str);
            if (tmp_match == 0)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else if ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && (((tNodePtr)I->addr3)->data->type == typeNUMERIC))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            if (((tNodePtr)I->addr2)->data->value.intVal == (((tNodePtr)I->addr3)->data->value.intVal))
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else
        {
	  exit(SEMANTIC_OTHER_ERROR);
            return INTERPRET_ERROR;
        }
        break;
    case I_IF_LESS:
        /*porovnani mensi
         * [bool] [int/real/nill] [int/real/nill]
         * [bool] [string/nill] [string/nill]
         */
        if ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && ((((tNodePtr)I->addr3)->data->type == typeNUMERIC)))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            if ((((tNodePtr)I->addr2)->data->value.intVal) < (((tNodePtr)I->addr3)->data->value.intVal))
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
		return 1;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
		return 0;
            }
        }
        else if ((((tNodePtr)I->addr2)->data->type == typeSTRING) && ((((tNodePtr)I->addr3)->data->type == typeSTRING)))
        {
            if(strcmp(((((tNodePtr)I->addr2)->data->value.string)->str),(((tNodePtr)I->addr3)->data->value.string)->str) < 0)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
		return 1;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
		return 0;
            }
        }
        else
        {
	  exit(SEMANTIC_OTHER_ERROR);
            return INTERPRET_ERROR;
        }

        break;
    case I_IF_BIGGER:
        if ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && (((tNodePtr)I->addr3)->data->type == typeNUMERIC))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            if (((tNodePtr)I->addr2)->data->value.intVal > ((tNodePtr)I->addr3)->data->value.intVal)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else if ((((tNodePtr)I->addr2)->data->type == typeSTRING) && ((((tNodePtr)I->addr3)->data->type == typeSTRING)))
        {
            if(strcmp((((tNodePtr)I->addr2)->data->value.string)->str,(((tNodePtr)I->addr3)->data->value.string)->str) > 0)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else
        {
	  exit(SEMANTIC_OTHER_ERROR);
            return INTERPRET_ERROR;
        }
        break;
    case I_IF_EQUAL_LESS:
        if  ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && (((tNodePtr)I->addr3)->data->type == typeNUMERIC))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            if (((tNodePtr)I->addr2)->data->value.intVal <= ((tNodePtr)I->addr3)->data->value.intVal)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else if ((((tNodePtr)I->addr2)->data->type == typeSTRING) && ((((tNodePtr)I->addr3)->data->type == typeSTRING)))
        {
            if(strcmp((((tNodePtr)I->addr2)->data->value.string)->str,(((tNodePtr)I->addr3)->data->value.string)->str) <= 0)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else
        {
	  exit(SEMANTIC_OTHER_ERROR);
            return INTERPRET_ERROR;
        }
        break;
    case I_IF_EQUAL_BIGGER:
        if ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && ((((tNodePtr)I->addr3)->data->type == typeNUMERIC)))
        {
            ((tNodePtr)I->addr1)->data->type = typeBOOLEAN;
            if (((tNodePtr)I->addr2)->data->value.intVal >= ((tNodePtr)I->addr3)->data->value.intVal)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else if ((((tNodePtr)I->addr2)->data->type == typeSTRING) && ((((tNodePtr)I->addr3)->data->type == typeSTRING)))
        {
            if(strcmp((((tNodePtr)I->addr2)->data->value.string)->str,(((tNodePtr)I->addr3)->data->value.string)->str) >= 0)
            {
                if_true = 1;
                ((tNodePtr)I->addr1)->data->value.boolean = true;
            }
            else
            {
                if_true = 0;
                ((tNodePtr)I->addr1)->data->value.boolean = false;
            }
        }
        else
        {
	  exit(SEMANTIC_OTHER_ERROR);
            return INTERPRET_ERROR;
        }
        break;
    case I_FIND://TEST:FUNGUJE
	if((((tNodePtr)I->addr2)->data->type == typeSTRING) && (((tNodePtr)I->addr2)->data->type == typeSTRING))
	{
        ((tNodePtr)I->addr1)->data->type = typeNUMERIC;
        ((tNodePtr)I->addr1)->data->value.intVal = Find((((tNodePtr)I->addr2)->data->value.string)->str,(((tNodePtr)I->addr3)->data->value.string)->str);
	}
        break;
    case I_EXPO://TEST:FUNGUJE
        /*mocnina cisla na cislo
         * [real] [int/real] [int,real]
         * vysledek je v addr1 , druhe cislo je zaklad, treti mocnina*/
	if ((((((tNodePtr)I->addr2)->data->type)) == typeNUMERIC) && (((((tNodePtr)I->addr3)->data->type)) == typeNUMERIC))
	{
        (((tNodePtr)I->addr1)->data->type) = typeNUMERIC;
        ((tNodePtr)I->addr1)->data->value.intVal = pow(((tNodePtr)I->addr2)->data->value.intVal,((tNodePtr)I->addr3)->data->value.intVal);
	}
	else
	{
	  return SEMANTIC_VARIABLE_ERROR;
	}
        break;
    case I_MULTIPLY://TEST:FUNGUJE
        /* [real/int] [real/int real/int]
         * nasobeni dvou cisel, prvni je vysledek, druhe a treti jsou nasobitel a nasobenec*/
        if ((((tNodePtr)I->addr2)->data->type == typeSTRING) && (((tNodePtr)I->addr3)->data->type == typeNUMERIC))
        {
          (((tNodePtr)I->addr1)->data->type) = typeSTRING;
        if (((tNodePtr)I->addr3)->data->value.intVal == 0.0)
        {
            (((tNodePtr)I->addr1)->data->value.string)->str = "";
        }
        else if (((tNodePtr)I->addr3)->data->value.intVal > 0.0)
        {
	  strInit(&(((tNodePtr)I->addr1)->data->value.string));
            for (int i=0; i< (int)((tNodePtr)I->addr3)->data->value.intVal; i++)
            {
                int k = 0;
                while(((((tNodePtr)I->addr2)->data->value.string)->str[k] != '\0'))
                {
                    addCharToWord(&(((tNodePtr)I->addr1)->data->value.string), ((((tNodePtr)I->addr2)->data->value.string)->str[k]));
                    k++;
                }
            }
        }  
        }
        if ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && (((tNodePtr)I->addr3)->data->type == typeNUMERIC))
        {
            (((tNodePtr)I->addr1)->data->type) = typeNUMERIC;
            ((tNodePtr)I->addr1)->data->value.intVal= (((tNodePtr)I->addr2)->data->value.intVal) * (((tNodePtr)I->addr3)->data->value.intVal);
        }
        else
	{
	  return SEMANTIC_VARIABLE_ERROR;
	}
        break;

      case I_SLICE:
      {
	tString *tmp_string;
	strInit(&tmp_string);
      if ((((((tNodePtr)I->addr2)->data->type)) == typeNUMERIC) && (((((tNodePtr)I->addr3)->data->type)) == typeNUMERIC))
	if(((tNodePtr)I->addr1)->data->type == typeSTRING)
	{
	  cutString(&((((tNodePtr)I->addr1)->data->value.string)),&(tmp_string),(((tNodePtr)I->addr2)->data->value.intVal),(((tNodePtr)I->addr3)->data->value.intVal));
	  ((((tNodePtr)I->addr1)->data->value.string)) = tmp_string;
	}
	 break;
      }
    case I_DIVIDE://TEST:FUNGUJE
        /*[real] [real/int real/int]
         * deleni dvou cisel, prvni cislo je vysledek, druhe delenec, treti delitel*/
        if ((((tNodePtr)I->addr2)->data->type == typeSTRING) || (((tNodePtr)I->addr3)->data->type == typeSTRING))
        {
            return SEMANTIC_VARIABLE_ERROR;
        }
        if ((((tNodePtr)I->addr2)->data->type == typeNUMERIC) && (((tNodePtr)I->addr3)->data->type == typeNUMERIC))
        {
            if ((((tNodePtr)I->addr3)->data->value.intVal) == 0)
            {
                return DIVIDE_ZERO_ERROR;
            }
            else
            {
                (((tNodePtr)I->addr1)->data->type) = typeNUMERIC;
                ((tNodePtr)I->addr1)->data->value.intVal= (((tNodePtr)I->addr2)->data->value.intVal) / (((tNodePtr)I->addr3)->data->value.intVal);
               // printf("deleni probehl uspesne %f\n",((tNodePtr)I->addr1)->data->value.intVal);
            }
        }
        break;
    case I_IF_CONDITION://TEST:FUNGUJE na dosavadnich lehkych testech jedna podminka, jeden else
//printf("1--%d\n",((instrList->active)->nextItem)->Instruction.instType);
        listNext(instrList);
        I = listData(instrList);
        if (I == NULL)
        {
            return 0;
        }
	//printf("1--%d\n",((instrList->active)->nextItem)->Instruction.instType);
        executeInstr(instrList,I);
	//printf("2--%d\n",((instrList->active)->nextItem)->Instruction.instType);
        //budeme provadet kod tak dlouho, dokud nenarazime na END3
        if (if_true == true)
        {
            if_true = 0;
	    //printf("3--%d\n",((instrList->active)->nextItem)->Instruction.instType);
            while((((instrList->active)->nextItem)->Instruction.instType != I_END_IF)
                    && (((instrList->active)->nextItem)->Instruction.instType != I_IF_ELSE))
            {
	           //   printf("mno");
                listNext(instrList);
                I = listData(instrList);
                if (I == NULL)
                {
                    return 0;
                }
                if((((instrList->active)->nextItem)->Instruction.instType == I_FUNCTION))
                    instrList->instrReturn = (instrList->active);
                executeInstr(instrList,I);
            }
            while(((instrList->active)->nextItem)->Instruction.instType != I_END_IF)
            {
		
                listNext(instrList);
		//printf("4--%d\n",((instrList->active)->nextItem)->Instruction.instType);
            }
            //printf("** %d **,%d",(instrList->active)->Instruction.instType,((instrList->active)->nextItem)->Instruction.instType);
            if (((instrList->active)->Instruction.instType == I_IF_ELSE) && (((instrList->active)->nextItem)->Instruction.instType == I_END_IF))
	    {
	      listNext(instrList);
	   if((((instrList->active)->nextItem)->Instruction.instType == I_IF_ELSE))	
	    while(((instrList->active)->nextItem)->Instruction.instType != I_END_IF)
	    {
	      listNext(instrList);
	    }
	    }
        }
        else
        {
            //pokud najdeme else
            while(((instrList->active)->nextItem)->Instruction.instType != I_IF_ELSE)
            {
                listNext(instrList);
            }
            while(((instrList->active)->nextItem)->Instruction.instType != I_END_IF)
            {
                listNext(instrList);
                I = listData(instrList);
                if (I == NULL)
                {
                    return 0;
                }
                if((((instrList->active)->nextItem)->Instruction.instType == I_FUNCTION))
                    instrList->instrReturn = (instrList->active);
                executeInstr(instrList,I);
            }
           listNext(instrList); 
           if (((instrList->active)->Instruction.instType == I_IF_ELSE) && (((instrList->active)->nextItem)->Instruction.instType == I_END_IF))
	    {
	      listNext(instrList);
	   if((((instrList->active)->nextItem)->Instruction.instType == I_IF_ELSE))	
	    while(((instrList->active)->nextItem)->Instruction.instType != I_END_IF)
	    {
	      listNext(instrList);
	    }
	    }
        }
        //printf("** %d **,%d",(instrList->active)->Instruction.instType,((instrList->active)->nextItem)->Instruction.instType);
        break;
    case I_HELP:
      l++;
      break;
    case I_WHILE://TEST:FUNGUJE na jednoduchych cyklech, nezkousel sem tezsi
{
  int podm;
	if(whilefce[l] != instrList->active)
	{
	  whilefce[l] = instrList->active;
	  if(l >= 9)
	  {
	    whilefce =realloc(whilefce, (size + maxSizeWhile) * sizeof(tListItem));
	  }
	}
	//printf("%d -- %d",l,(((instrList->active)->nextItem)->Instruction.instType));
	//instrList->instrWhile = instrList->active;
       // tokListPush(&sWhile,instrList->active);
// 	instrList->active = tokListPop(&sWhile);
	listNext(instrList);
        I = listData(instrList);
        if (I == NULL)
        {
            return 0;
        }
        podm = executeInstr(instrList,I);
        if (podm == 1)
        {
            if_true = 0;
            while((((instrList->active)->nextItem)->Instruction.instType != I_END_WHILE))
	     //dodano
            {    
	      /*if((((instrList->active)->nextItem)->Instruction.instType != I_HELP))
	      {
		break;
	      }*/
                listNext(instrList);
                I = listData(instrList);
                if (I == NULL)
                {
                    return 0;
                }
               /* if((((instrList->active)->nextItem)->Instruction.instType == I_FUNCTION))
		{
		  instrList->instrReturn = (instrList->active);
		}*/
                executeInstr(instrList,I);
		  //              listNext(instrList);
                //I = listData(instrList);
            }
            /*if((((instrList->active)->nextItem)->Instruction.instType == I_WHILE))
	    {
	      listNext(instrList);
                I = listData(instrList);
                if (I == NULL)
                {
                    return 0;
                }
               executeInstr(instrList,I);
	    }*/
            //skok na zacatek v seznamu instrukci
        }
        else
        {
            while(((instrList->active)->nextItem)->Instruction.instType != I_END_WHILE)
            {
                listNext(instrList);
                I = listData(instrList);
		while_func = 1;
                if (I == NULL)
                {
                    return 0;
                }
            }
                        l--;
            listNext(instrList);
            I = listData(instrList);
            if (I == NULL)
            {
                return 0;
            }
	    //(instrList->active) = whilefce[l];
            return 0;
        }
        //(instrList->active) = instrList->instrWhile;
       // (instrList->active) = tokListTop(&sWhile);
        //instrList->instrWhile;
        (instrList->active) = whilefce[l];
        while_func = 1;
}
        break;
	case I_END_WHILE:
	  //(instrList->active) = whilefce[l];
	  break;
    case I_RETURN:
        break;
    case I_FUNCTION://TEST:FUGNUJE
    listFirst(((tNodePtr)I->addr1)->data->value.func->instr);
if(listData(((tNodePtr)I->addr1)->data->value.func->instr)==NULL)
    {   
     if (strcmp(((tNodePtr)I->addr1)->key, "print")==0)
     {
      writeINT(I);
     }
     if (strcmp(((tNodePtr)I->addr1)->key, "sort")==0)
     {
       sortINT(I);
     }
     if (strcmp(((tNodePtr)I->addr1)->key, "input")==0)
     {
       inputINT(I);
     }
     if (strcmp(((tNodePtr)I->addr1)->key, "find")==0)
     {
       kmpINT(I);
     }
     if (strcmp(((tNodePtr)I->addr1)->key, "numeric")==0)
     {
       numericINT(I);
     }
     break;
    }
    else
    {
    Itmp = getIList((tNodePtr)I->addr1, (struct dataArr *)I->addr2);
    instrListConnect(instrList,Itmp);
    }
        break;
    case I_ASSIGN://TEST:FUNGUJE aspon na real, nezkousel sem jeste bool ni string
        /*priradit kam, hodnota*/
	if((((tNodePtr)I->addr2)->data->type) == typeNUMERIC)
	{
	 // (((tNodePtr)I->addr1)->data->type) = typeNUMERIC;
	  (((tNodePtr)I->addr1)->data->value.intVal) = (((tNodePtr)I->addr2)->data->value.intVal);
	}
	else if(((tNodePtr)I->addr2)->data->type == typeBOOLEAN)
	{
	//  (((tNodePtr)I->addr1)->data->type) = typeBOOLEAN;
	  (((tNodePtr)I->addr1)->data->value.boolean) = (((tNodePtr)I->addr2)->data->value.boolean);
	}
	else if(((tNodePtr)I->addr2)->data->type == typeSTRING)
	{
	//  (((tNodePtr)I->addr1)->data->type) = typeSTRING;
	  if(((((tNodePtr)I->addr1)->data->value.string) = malloc(sizeof(tString)))== NULL)
	  {
	    return INTERPRET_ERROR;
	  }
	  (((tNodePtr)I->addr1)->data->value.string)->str = (((tNodePtr)I->addr2)->data->value.string)->str;
	}
	else
	{
	  return SEMANTIC_VARIABLE_ERROR;
	}
        break;
    case I_RETURN_MAIN:
        break;
    }
    return 0;
}

int interpretMain(tListInstr *instrList)
{
    listFirst(instrList);
    tInstr *I;
    while ((instrList->active)!= NULL)
    {
     //printf("%d-",(instrList->active)->Instruction.instType);
      listNext(instrList);
    }
    listFirst(instrList);
    while ((instrList->active)!= NULL)
    {
        I = listData(instrList);
        if (I == NULL)
        {
            return 0;
        }
        executeInstr(instrList,I);
        if (while_func == 0)
            listNext(instrList);
        while_func = 0;
    }
    //printf("END OF INSTRUCTIONS.\n");
    return 0;
}
int structInit(tDataPtr *structure)
{
    if(((*structure) = malloc(sizeof(tDataPtr)))== NULL)
    {
      exit(SEMANTIC_OTHER_ERROR);
        return INTERPRET_ERROR;
    }
    return 0;
}

void writeINT(tInstr *I)
{
  int i = 0;
  while(((struct dataArr *)I->addr2)->count != i)
  {
    i++;
        if ((((struct dataArr *)I->addr2)->param[i-1]->data->type != typeNUMERIC)
	  && (((struct dataArr *)I->addr2)->param[i-1]->data->type != typeSTRING)
	  && (((struct dataArr *)I->addr2)->param[i-1]->data->type != typeBOOLEAN)
	  && (((struct dataArr *)I->addr2)->param[i-1]->data->type != typeNULL)
	)
	  exit(SEMANTIC_VARIABLE_ERROR);
  switch (((struct dataArr *)I->addr2)->param[i-1]->data->type)
    {
	case typeSTRING:
	  translate(((((struct dataArr *)I->addr2)->param[i-1]->data->value.string)->str));
	  printf("%s", ((((struct dataArr *)I->addr2)->param[i-1]->data->value.string)->str));
	 break;
	case typeNUMERIC:
	  printf("%g", ((struct dataArr *)I->addr2)->param[i-1]->data->value.intVal);
	 break;
	case typeNULL:
	  if((((struct dataArr *)I->addr2)->param[i-1]->data->value.boolean) == 1)
	  printf("True");
	  else
	  printf("False");
	 break;
    }
  }
  return;
}
void numericINT(tInstr *I)
{
        (((tNodePtr)I->addr1)->data->type) = typeNUMERIC;
        ((tNodePtr)I->addr1)->data->value.intVal = numeric((((tNodePtr)I->addr2)->data->value.string));
	return;
}
void inputINT(tInstr *I)
{
        (((tNodePtr)I->addr1)->data->type) = typeSTRING;
        (((tNodePtr)I->addr1)->data->value.string) = malloc(sizeof(tDataPtr));
        (*((tNodePtr)I->addr1)->data->value.string) = input();
	return;
}
void kmpINT(tInstr *I)
{
        ((tNodePtr)I->addr1)->data->type = typeNUMERIC;
        ((tNodePtr)I->addr1)->data->value.intVal = Find((((tNodePtr)I->addr2)->data->value.string)->str,(((tNodePtr)I->addr3)->data->value.string)->str);
	return;
}
void sortINT(tInstr *I)
{
        ((tNodePtr)I->addr2)->data->type = typeSTRING;
        (((tNodePtr)I->addr1)->data->value.string)->str=sort((((tNodePtr)I->addr2)->data->value.string)->str);
	return;
}
void lenINT(tInstr *I)
{
  ((tNodePtr)I->addr1)->data->value.intVal = len(((tNodePtr)I->addr1)->data->value.string);
  return;
}
void typeOfINT(tInstr *I)
{
  switch(((tNodePtr)I->addr1)->data->type)
  {
    case typeNUMERIC:
      ((tNodePtr)I->addr1)->data->value.intVal = 3.0;
      break;
    case typeSTRING:
      ((tNodePtr)I->addr1)->data->value.intVal = 8.0;
      break;
    case typeBOOLEAN:
      ((tNodePtr)I->addr1)->data->value.intVal = 1.0;
      break;
    case typeNULL:
      ((tNodePtr)I->addr1)->data->value.intVal = 0.0;
      break;
    case typeFUNC:
      ((tNodePtr)I->addr1)->data->value.intVal = 6.0;
      break;
  }
  return;
}


char *translate(char *stringTMP)
{
      char *here=stringTMP;
      size_t len=strlen(stringTMP);
      unsigned int num;
      int numlen;

      while (NULL!=(here=strchr(here,'\\')))
      {
            numlen=1;
            switch (here[1])
            {
            case '\\':
                  break;
             case '\"':
                  *here = '\"';
                  break;     

            case 'r':
                  *here = '\r';
                  break;

            case 'n':
                  *here = '\n';
                  break;

            case 't':
                  *here = '\t';
                  break;

            case 'v':
                  *here = '\v';
                  break;

            case 'a':
                  *here = '\a';
                  break;

            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
                  numlen = sscanf(here,"%o",&num);
                  *here = (char)num;
                  break;

           case 'x':
                  numlen = sscanf(here+2,"%2x",&num);
                  numlen += 2;
                  *here = (char) num;
                  break;
            }
            num = here - stringTMP + numlen;
            here++;
            memmove(here,here+numlen,len-num );
      }
      return stringTMP;
}

