package com.example.heli.searchengine;

/**
 * Created by Heet on 09-04-2016.
 */
import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;

import java.util.ArrayList;
public class CustomAdapter1 extends BaseAdapter {
    private LayoutInflater inflater;

    private Context context;
    // As the name suggests, its the context of current state of the application/object. It lets newly created objects understand what has been going on.
    // Typically you call it to get information regarding another part of your program (activity, package/application)
    private ArrayList<Main3Activity.Documents> documentsArrayList;

    public CustomAdapter1(Context context, ArrayList<Main3Activity.Documents> documentsArrayList) {
        this.context=context;
        this.documentsArrayList=documentsArrayList;
        Log.i("documents", String.valueOf(this.documentsArrayList));
        /* Layout Inflater to call external xml layout () */
        inflater = (LayoutInflater)context.
                getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        //Basically it is needed to create (or fill) View based on XML file in runtime.
    }

    @Override
    public int getCount() {
        if(documentsArrayList.size()<=0)
            return 1;
        return documentsArrayList.size();
    }

    @Override
    public Object getItem(int  position) {
        return position;
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup viewGroup) {

        //convertView is used to reuse old view.
        View vi = convertView;
        ViewHolder holder;
        if(convertView==null){

            /****** Inflate card_view.xml file for each row ( Defined below ) *******/
            vi = inflater.inflate(R.layout.list_item, null);

            /****** View Holder Object to contain card_view.xml file elements ******/

            holder = new ViewHolder();
            holder.url = (TextView) vi.findViewById(R.id.url);
            holder.title=(TextView)vi.findViewById(R.id.title);
            holder.caption=(TextView)vi.findViewById(R.id.caption);
            holder.DoC=(TextView)vi.findViewById(R.id.DoC);

            holder.imageView=(ImageView)vi.findViewById(R.id.image);

            /************  Set holder with LayoutInflater ************/
            vi.setTag( holder );
        }else {
            holder = (ViewHolder) vi.getTag();
        }

        if(documentsArrayList.size()<=0)
        {
            holder.title.setText("Oops..No Data Found..!!!");
            holder.url.setVisibility(View.GONE);
            holder.caption.setVisibility(View.GONE);
            holder.DoC.setVisibility(View.GONE);
            holder.imageView.setVisibility(View.GONE);

        }
        else {

            Main3Activity.Documents document=documentsArrayList.get(position);
            holder.url.setText("http://192.168.227.1:8082/ita_dse/docs/" + document.url);
            holder.title.setText(document.title);
            holder.caption.setText(document.caption);
            holder.DoC.setText("created on " + document.DoC);

            Glide.with(context)
                    .load("http://192.168.227.1:8082/ita_dse/images/" + document.thumbnail)
                    .into(holder.imageView);
        }

        return vi;
    }


    /********* Create a holder Class to contain inflated xml file elements *********/
    public static class ViewHolder{

        public TextView url;
        public TextView title;
        public TextView caption;
        public TextView DoC;

        public ImageView imageView;

    }

}
