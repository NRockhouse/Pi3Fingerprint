#include <stdio.h>
#include <stdlib.h>
#include <dirent.h>
#include <string.h>
#include <libfprint/fprint.h>

void updategui(const char *data) {
        FILE *file = fopen("../gui/data.txt","w");
        fprintf(file,data);
        fclose(file);
        system("sudo -u pi DISPLAY=:0 xdotool key F5");
}

int main() {
        int tmp = fp_init();
        if(tmp != 0) {
                fprintf(stderr, "fprintd init error, error code: %d\n", tmp);
                return -1;
        }

        struct fp_dscv_dev **devs = fp_discover_devs();
        struct fp_dscv_dev *dscv_dev = devs[0];
        struct fp_dev *dev = fp_dev_open(dscv_dev);

        struct fp_print_data *fingers[150];
	memset(fingers, 0, sizeof(fingers[0]) * 150);
        int fingercount = 0;
        char fingerids[100][10];
	memset(fingerids, 0, sizeof(fingerids[0]) * 100);

        DIR *FD = opendir("../fingers/");
        struct dirent *curfile;

        while((curfile = readdir(FD))) {
                // ignore cureent directory and parent directory pseudo-folder
                if(!strcmp(curfile->d_name,".") || !strcmp(curfile->d_name,".."))
                        continue;

                strcpy(fingerids[fingercount], curfile->d_name);

                long length;
                char *fbuf = 0;
                char fullpath[100];
                snprintf(fullpath, sizeof(fullpath), "../fingers/%s", curfile->d_name);
                FILE *f = fopen(fullpath, "rb");
                fseek(f, 0, SEEK_END);
                length = ftell(f);
                fseek(f, 0, SEEK_SET);
                fbuf = malloc(length);
                fread(fbuf, 1, length, f);
                fclose(f);
                fingers[fingercount] = fp_print_data_from_data(fbuf, length);
                free(fbuf);
                fingercount++;
        }

	closedir(FD);

	int result;
	int retcode = fp_identify_finger(dev, fingers, &result);

	if(retcode == FP_VERIFY_NO_MATCH) {
		updategui("unrecognised.php|No matching fingerprint is found.");
	} else if(retcode == FP_VERIFY_MATCH) {
		char str[256];
		printf("%d %s", result, fingerids[result]);
		snprintf(str, sizeof(str), "welcome.php|%s", fingerids[result]);
		updategui(str);
	} else if(retcode < -1) {
		updategui("unrecognised.php|FATAL ERROR: Couldn't interface with scanner, retrying in 6 seconds.");
	} else {
		updategui("unrecognised.php|Fingerprint couldn't be obtained, please retry.");
	}
	
    fp_dev_close(dev);
    fp_dscv_devs_free(devs);
    fp_exit();
    printf("\n");
    return 0;
}
